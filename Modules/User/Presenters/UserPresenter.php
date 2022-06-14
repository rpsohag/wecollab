<?php

namespace Modules\User\Presenters;

use Ohswedd\Presenter\Presenter;

class UserPresenter extends Presenter
{
    /**
     * Return the gravatar link for the users email
     * @param  int $size
     * @return string
     */
    public function gravatar($size = 90)
    {
        $email = md5($this->email);

        return "https://www.gravatar.com/avatar/$email?s=$size";
    }

    /**
     * @return string
     */
    public function fullname()
    {
        return $this->name ?: $this->first_name . ' ' . $this->last_name;
    }
}
