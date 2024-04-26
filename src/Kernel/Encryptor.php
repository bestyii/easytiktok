<?php

namespace EasyTiktok\Kernel;

use EasyTiktok\Kernel\Exceptions\RuntimeException;
use EasyTiktok\Kernel\Support\AES;

/**
 * Class Encryptor.
 *
 * @author overtrue <i@overtrue.me>
 */
class Encryptor {
    public const ERROR_INVALID_SIGNATURE = -40001; // Signature verification failed
    public const ERROR_PARSE_XML = -40002; // Parse XML failed
    public const ERROR_CALC_SIGNATURE = -40003; // Calculating the signature failed
    public const ERROR_INVALID_AES_KEY = -40004; // Invalid AESKey
    public const ERROR_INVALID_APP_ID = -40005; // Check AppID failed
    public const ERROR_ENCRYPT_AES = -40006; // AES EncryptionInterface failed
    public const ERROR_DECRYPT_AES = -40007; // AES decryption failed
    public const ERROR_INVALID_XML = -40008; // Invalid XML
    public const ERROR_BASE64_ENCODE = -40009; // Base64 encoding failed
    public const ERROR_BASE64_DECODE = -40010; // Base64 decoding failed
    public const ERROR_XML_BUILD = -40011; // XML build failed
    public const ILLEGAL_BUFFER = -41003; // Illegal buffer

    /**
     * App id.
     *
     * @var string
     */
    protected $appId;

    /**
     * App token.
     *
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $aesKey;

    /**
     * Block size.
     *
     * @var int
     */
    protected $blockSize = 32;

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string|null $token
     * @param string|null $aesKey
     */
    public function __construct(string $appId, string $token = null, string $aesKey = null) {
        $this->appId = $appId;
        $this->token = $token;
        $this->aesKey = base64_decode($aesKey . '=', true);
    }

    /**
     * Get the app token.
     *
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * Decrypt message.
     *
     * @param string $content
     * @param string $msgSignature
     * @param string $nonce
     * @param string $timestamp
     *
     * @return string
     *
     * @throws RuntimeException|Exceptions\InvalidArgumentException
     */
    public function decrypt(string $content, string $msgSignature, string $nonce, string $timestamp): string {
        $signature = $this->signature($this->token, $timestamp, $nonce, $content);

        if ($signature !== $msgSignature) {
            throw new RuntimeException('Invalid Signature.', self::ERROR_INVALID_SIGNATURE);
        }

        $decrypted = AES::decrypt(
            base64_decode($content, true),
            $this->aesKey,
            substr($this->aesKey, 0, 16),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
        );
        $result = $this->pkcs7Unpad($decrypted);
        $content = substr($result, 16, strlen($result));
        $contentLen = unpack('N', substr($content, 0, 4))[1];

        if (trim(substr($content, $contentLen + 4)) !== $this->appId) {
            throw new RuntimeException('Invalid appId.', self::ERROR_INVALID_APP_ID);
        }

        return substr($content, 4, $contentLen);
    }

    /**
     * Get SHA1.
     *
     * @return string
     */
    public function signature(): string {
        $array = func_get_args();
        sort($array, SORT_STRING);

        return sha1(implode($array));
    }

    /**
     * PKCS#7 pad.
     *
     * @param string $text
     * @param int $blockSize
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function pkcs7Pad(string $text, int $blockSize): string {
        if ($blockSize > 256) {
            throw new RuntimeException('$blockSize may not be more than 256');
        }
        $padding = $blockSize - (strlen($text) % $blockSize);
        $pattern = chr($padding);

        return $text . str_repeat($pattern, $padding);
    }

    /**
     * PKCS#7 unpad.
     *
     * @param string $text
     *
     * @return string
     */
    public function pkcs7Unpad(string $text): string {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > $this->blockSize) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }
}
