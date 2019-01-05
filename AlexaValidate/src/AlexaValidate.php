<?php

class OutdatedCertExceptionException extends \Exception
{
}
class RequestInvalidSignatureException extends \Exception
{
}
class RequestInvalidTimestampException extends \Exception
{
}

class AlexaValidate
{

	protected $timestampTolerance = 150;

	public function __construct()
	{
	}

	/**
	 * Validate request data.
	 *
	 * @param Request $request
	 */
	public function validate($request)
	{
		$this->validateTimestamp($request);
		try {
			$this->validateSignature($request);
		} catch (OutdatedCertExceptionException $e) {
			// load cert again and validate because temp file was outdatet.
			$this->validateSignature($request);
		}
	}

	private function validateTimestamp($request)
	{
		$params = json_decode($request, true);
		if (null === $params['request']) {
			return;
		}

		$dateTime = new DateTime($params['request']['timestamp']);
		$differenceInSeconds = time() - $dateTime->getTimestamp();

		if ($differenceInSeconds > $this->timestampTolerance) {
			throw new RequestInvalidTimestampException('Invalid timestamp.');
		}
	}

	private function validateSignature($request)
	{
		$params = json_decode($request, true);
		if (null === $params['request']) {
			return;
		}

		$signatureCertChainUrl = $_SERVER['HTTP_' . str_replace('-', '_', strtoupper('SignatureCertChainUrl'))];
		$signature = $_SERVER['HTTP_' . str_replace('-', '_', strtoupper('Signature'))];

		// validate cert url
		if (false === (bool) preg_match("/https:\/\/s3.amazonaws.com(\:443)?\/echo.api\/*/i", $signatureCertChainUrl)) {
			throw new RequestInvalidSignatureException('Invalid cert url.');
		}

		// check if pem file is already downloaded to temp or download.
		$localCertPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.md5($signatureCertChainUrl).'.pem';
		if (!file_exists($localCertPath)) {
			$certData = @file_get_contents($signatureCertChainUrl);
			@file_put_contents($localCertPath, $certData);
		} else {
			$certData = @file_get_contents($localCertPath);
		}

		// openssl cert validation
		if (1 !== @openssl_verify($request, base64_decode($signature, true), $certData, 'sha1')) {
			throw new RequestInvalidSignatureException('Cert ssl verification failed.');
		}

		// parse cert
		$cert = @openssl_x509_parse($certData);
		if (empty($cert)) {
			throw new RequestInvalidSignatureException('Parse cert failed.');
		}

		// validate cert subject
		if (false === isset($cert['extensions']['subjectAltName']) ||
				false === stristr($cert['extensions']['subjectAltName'], 'echo-api.amazon.com')
				) {
					throw new RequestInvalidSignatureException('Cert subject error.');
				}

				// validate cert validTo time
				if (false === isset($cert['validTo_time_t']) || time() > $cert['validTo_time_t'] || false === isset($cert['validFrom_time_t']) || time() < $cert['validFrom_time_t']) {
					if (file_exists($localCertPath)) {
						@unlink($localCertPath);
					}
					throw new OutdatedCertExceptionException('OutdatedCertException');
				}
	}

}