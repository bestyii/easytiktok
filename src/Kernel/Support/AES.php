<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyTiktok\Kernel\Support;

use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;

/**
 * Class AES.
 *
 * @author overtrue <i@overtrue.me>
 */
class AES {
    /**
     * @param string $text
     * @param string $key
     * @param string $iv
     * @param int $option
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public static function encrypt(string $text, string $key, string $iv, int $option = OPENSSL_RAW_DATA): string {
        self::validateKey($key);
        self::validateIv($iv);

        return openssl_encrypt($text, self::getMode($key), $key, $option, $iv);
    }

    /**
     * @param string $cipherText
     * @param string $key
     * @param string $iv
     * @param int $option
     * @param string|null $method
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public static function decrypt(string $cipherText, string $key, string $iv, int $option = OPENSSL_RAW_DATA, $method = null): string {
        self::validateKey($key);
        self::validateIv($iv);

        return openssl_decrypt($cipherText, $method ?: self::getMode($key), $key, $option, $iv);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function getMode(string $key): string {
        return 'aes-' . (8 * strlen($key)) . '-cbc';
    }

    /**
     *
     * @param string $key
     * @throws InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function validateKey(string $key): void {
        if (!in_array(strlen($key), [16, 24, 32], true)) {
            throw new InvalidArgumentException(sprintf('Key length must be 16, 24, or 32 bytes; got key len (%s).', strlen($key)));
        }
    }

    /**
     *
     * @param string $iv
     * @throws InvalidArgumentException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public static function validateIv(string $iv): void {
        if (!empty($iv) && 16 !== strlen($iv)) {
            throw new InvalidArgumentException('IV length must be 16 bytes.');
        }
    }
}
