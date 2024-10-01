<?php
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;


class JwtService {
    private $configuration;

    public function __construct() {
        // Cấu hình signer HMAC-SHA256 với khóa bí mật
        $this->configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('your-secret-key') // Thay bằng khóa bí mật của bạn
        );
    }

    /**
     * Hàm tạo token JWT
     * @param array $userData Thông tin người dùng
     * @return string Token JWT đã ký
     */
    public function generateToken(array $userData): string {        
        $token = $this->configuration->builder()
            ->identifiedBy('4f1g23a12aa', true)
            ->withClaim('uid', $userData['id'])
            ->withClaim('email', $userData['email'])
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());
        
        return $token->toString();
    }

    /**
     * Hàm xác thực token JWT
     * @param string $tokenString Token JWT cần xác thực
     * @return array|bool Trả về dữ liệu người dùng nếu token hợp lệ, false nếu không hợp lệ
     */
    public function validateToken(string $tokenString) {
        try {
            $token = $this->configuration->parser()->parse($tokenString);

            $validator = $this->configuration->validator();
            
            $constraints = [
                new SignedWith($this->configuration->signer(), $this->configuration->signingKey()) // Kiểm tra chữ ký
            ];

            if (!$validator->validate($token, ...$constraints)) {
                return false;
            }

            return [
                'uid' => $token->claims()->get('uid'),
                'email' => $token->claims()->get('email'),
            ];

        } catch (\Exception $e) {
            return false;
        }
    }


}

