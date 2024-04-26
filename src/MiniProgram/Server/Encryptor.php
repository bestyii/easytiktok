<?php

namespace EasyTiktok\MiniProgram\Server;

use EasyTiktok\Kernel\Encryptor as BaseEncryptor;
use EasyTiktok\Kernel\Exceptions\DecryptException;
use EasyTiktok\Kernel\Exceptions\InvalidArgumentException;
use EasyTiktok\Kernel\Support\AES;

/**
 * Class Encryptor.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Encryptor extends BaseEncryptor {
    /**
     * Decrypt data.
     *
     * @param string $sessionKey
     * @param string $iv
     * @param string $encrypted
     *
     * @return array
     *
     * @throws DecryptException|InvalidArgumentException
     */
    public function decryptData(string $sessionKey, string $iv, string $encrypted): array {
        $decrypted = AES::decrypt(
            base64_decode($encrypted, false),
            base64_decode($sessionKey, false),
            base64_decode($iv, false)
        );

        $decrypted = json_decode($decrypted, true);

        if (!$decrypted) {
            throw new DecryptException('The given payload is invalid.');
        }

        return $decrypted;
    }
}
