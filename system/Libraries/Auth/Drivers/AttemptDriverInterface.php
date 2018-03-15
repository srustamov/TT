<?php namespace System\Libraries\Auth\Drivers;




interface AttemptDriverInterface
{
  public function getAttemptsCountOrFail($guard);

  public function addattempt($guard);

  public function startLockTime($guard,$lock_time);

  public function deleteAttempt($guard);

  public function expireTimeOrFail($guard);

  public function getRemainingSecondsOrFail($guard);
}
