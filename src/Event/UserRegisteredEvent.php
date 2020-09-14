<?php

namespace App\Event;

use App\Entity\User;

class UserRegisteredEvent
{
  public const NAME = 'user.register';

  /**
   * UserConfirmationSubscriber constructor.
   * @param UserConfirmationService $confirmationService
   */
  protected $userRegistered;

  public function __construct(User $userRegistered)
  {
    $this->userRegistered = $userRegistered;
  }

  public function getUserRegistered()
  {
    return $this->userRegistered;
  }
}
