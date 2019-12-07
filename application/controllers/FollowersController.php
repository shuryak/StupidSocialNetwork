<?php

namespace application\controllers;
use application\core\Controller;
use application\core\ErrorCodes;
use application\lib\Token;
use application\lib\JsonData;

class FollowersController extends Controller {

    public static function isFollowedApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['follower', 'following']);

        if(empty($dataCheckResult)) {
            $follower = (int)$data->follower;
            $following = (int)$data->following;

            $result = self::$model::isFollowed($follower, $following);

            if(isset($result['response'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => $result['response'],
                    )
                );
            } elseif(isset($result['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                    )
                );
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function followApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['access_token', 'following']);

        if(empty($dataCheckResult)) {
            $accessToken = $data->access_token;
            $following = (int)$data->following;

            $decoded = Token::decodeAccessToken($accessToken);

            if(isset($decoded['content'])) {
                $result = self::$model::follow((int)$decoded['content']['id'], $following);

                if(isset($result['response'])) {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'response' => true,
                        )
                    );
                } else {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                        )
                    );
                }
            } elseif(isset($decoded['error'])) {
                if($decoded['error'] == Token::EXPIRED_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::EXPIRED_ACCESS_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
                if($decoded['error'] == Token::INVALID_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::INVALID_ACCESS_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function unfollowApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['access_token', 'following']);

        if(empty($dataCheckResult)) {
            $accessToken = $data->access_token;
            $following = (int)$data->following;

            $decoded = Token::decodeAccessToken($accessToken);

            if(isset($decoded['content'])) {
                $result = self::$model::unfollow((int)$decoded['content']['id'], $following);

                if(isset($result['response'])) {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'response' => true,
                        )
                    );
                } else {
                    http_response_code($result['http']);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                        )
                    );
                }
            } elseif(isset($decoded['error'])) {
                if($decoded['error'] == Token::EXPIRED_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::EXPIRED_ACCESS_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
                if($decoded['error'] == Token::INVALID_TOKEN) {
                    http_response_code(400);
                    echo json_encode(
                        array(
                            'error' => ['error_code' => ErrorCodes::INVALID_ACCESS_TOKEN, 'details' => $accessToken],
                        )
                    );
                    return;
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function getUserFollowersApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['id', 'offset', 'start']);

        if(empty($dataCheckResult) || (count($dataCheckResult) == 1 && !isset($dataCheckResult['start']))) {
            $id = (int)$data->id;
            $offset = (int)$data->offset;
            $start = isset($dataCheckResult['start']) ? 0 : (int)$data->start;

            $result = self::$model::getUserFollowers($id, $offset, $start);
            $followersCount = self::$model::getFollowersCount($id);

            if(isset($result['response']) && isset($followersCount['response'])) {
                $leftCount = $start >= $followersCount['response'] ? 0 : $followersCount['response'] - ($start + count($result['response']));
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => [
                            'left' => $leftCount,
                            'followers' => $result['response'],
                        ]
                    )
                );
            } elseif(isset($result['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                    )
                );
            } elseif(isset($followersCount['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $followersCount['error_code'], 'details' => $followersCount['details']],
                    )
                );
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function getUserFriendsApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['id', 'offset', 'start']);

        if(empty($dataCheckResult) || (count($dataCheckResult) == 1 && !isset($dataCheckResult['start']))) {
            $id = (int)$data->id;
            $offset = (int)$data->offset;
            $start = isset($dataCheckResult['start']) ? 0 : (int)$data->start;

            $result = self::$model::getUserFriends($id, $offset, $start);
            $friendsCount = self::$model::getFriendsCount($id);

            if(isset($result['response']) && isset($friendsCount['response'])) {
                $leftCount = $start >= $friendsCount['response'] ? 0 : $friendsCount['response'] - ($start + count($result['response']));
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => [
                            'left' => $leftCount,
                            'friends' => $result['response'],
                        ]
                    )
                );
            } elseif(isset($result['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                    )
                );
            } elseif(isset($friendsCount['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $friendsCount['error_code'], 'details' => $friendsCount['details']],
                    )
                );
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

    public static function getUserFollowingApi() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

        $data = json_decode(file_get_contents('php://input'));

        $dataCheckResult = JsonData::check($data, ['id', 'offset', 'start']);

        if(empty($dataCheckResult) || (count($dataCheckResult) == 1 && !isset($dataCheckResult['start']))) {
            $id = (int)$data->id;
            $offset = (int)$data->offset;
            $start = isset($dataCheckResult['start']) ? 0 : (int)$data->start;

            $result = self::$model::getUserFollowing($id, $offset, $start);
            $followingCount = self::$model::getFollowingCount($id);

            if(isset($result['response']) && isset($followingCount['response'])) {
                $leftCount = $start >= $followingCount['response'] ? 0 : $followingCount['response'] - ($start + count($result['response']));
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'response' => [
                            'left' => $leftCount,
                            'following' => $result['response'],
                        ]
                    )
                );
            } elseif(isset($result['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $result['error_code'], 'details' => $result['details']],
                    )
                );
            } elseif(isset($followingCount['error_code'])) {
                http_response_code($result['http']);
                echo json_encode(
                    array(
                        'error' => ['error_code' => $followingCount['error_code'], 'details' => $followingCount['details']],
                    )
                );
            }
        } else {
            http_response_code(400);
            echo json_encode(
                array(
                    'error' => ['error_code' => ErrorCodes::BAD_FIELDS, 'details' => false],
                )
            );
        }
    }

}