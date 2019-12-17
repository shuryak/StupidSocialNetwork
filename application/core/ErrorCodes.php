<?php

namespace application\core;

abstract class ErrorCodes {
  const BAD_FIELDS = 0;
  const UNKNOWN_INTERNAL_ERROR = 1;
  const EMAIL_ALREADY_EXISTS = 2;
  const EMAIL_IS_NOT_REGISTERED = 3;
  const ID_IS_NOT_REGISTERED = 4;
  const INCORRECT_LOGIN_OR_PASSWORD = 5;
  const INVALID_ACCESS_TOKEN = 6;
  const EXPIRED_ACCESS_TOKEN = 7;
  const INVALID_REFRESH_TOKEN = 8;
  const EXPIRED_REFRESH_TOKEN = 9;
  const USED_REFRESH_TOKEN = 10;
  const POST_DOES_NOT_EXIST = 11;
  const ANOTHER_POST = 12;
  const USERS_ARE_ALREADY_FRIENDS = 13;
  const USER_IS_ALREADY_FOLLOWED = 14;
  const IDENTICAL_VALUES = 15;
  const USER_IS_NOT_FOLLOWED = 16;
  const FOLLOWERS_DOES_NOT_EXISTS = 17;
  const FRIENDS_DOES_NOT_EXISTS = 18;
  const FOLLOWING_DOES_NOT_EXISTS = 19;
  const FILE_IS_TOO_LARGE = 20;
  const ANOTHER_SERVER = 21;
}