<?php namespace System\Libraries\Auth\Drivers;

interface AttemptDriverInterface
{
    public function getAttemptsCountOrFail();

    public function increment();

    public function startLockTime($lockTime);

    public function deleteAttempt();

    public function expireTimeOrFail();

    public function getRemainingSecondsOrFail();
}
