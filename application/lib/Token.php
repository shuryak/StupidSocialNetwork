<?php

namespace application\lib;
use \Firebase\JWT\JWT;
use Exception;

class Token {

    const INVALID_TOKEN = 0;
    const EXPIRED_TOKEN = 1;

    public static function createAccessToken($dataArray) {
        include 'application/lib/jwt-options.php';

        $token = array(
            'iss' => $iss,
            'aud' => $aud,
            'iat' => $iat,
            'exp' => $exp,
            'data' => $dataArray,
            );

        return JWT::encode($token, $key);
    }

    public static function createRefreshToken($dataArray) {
        include 'application/lib/jwt-options.php';

        $token = array(
            'iss' => $iss,
            'aud' => $aud,
            'iat' => $iat,
            'data' => $dataArray,
            );

        return JWT::encode($token, $privateKey, 'RS256');
    }

    public static function decodeAccessToken($accessToken) {
        include 'application/lib/jwt-options.php';

        try{
            $decoded = JWT::decode($accessToken, $key, array('HS256'));
        } catch(\Firebase\JWT\ExpiredException $e) {
            return array(
                'error' => self::EXPIRED_TOKEN,
            );
        } catch (Exception $e) {
            return array(
                'error' => self::INVALID_TOKEN,
            );
        }

        return array('content' => (array)$decoded->data);
    }

    public static function decodeRefreshToken($refreshToken) {
        include 'application/lib/jwt-options.php';
        
        try{
            $decoded = JWT::decode($refreshToken, $publicKey, array('RS256'));
        } catch(\Firebase\JWT\ExpiredException $e) {
            return array(
                'error' => self::EXPIRED_TOKEN,
            );
        } catch (Exception $e) {
            return array(
                'error' => self::INVALID_TOKEN,
            );
        }

        return array('content' => (array)$decoded->data);
    }
}