<?php

namespace App\Notifications;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\PasswordSetup;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class CustomerEmail extends Notification
{
    use Queueable;

    protected $project_id;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $notifiable;
        $token = Str::random(60);
        $hashed_token = bcrypt($token);
        $url = url(config('app.url') . route('password.setup', [$token], false));

        $project_name = Project::find($this->project_id)->name;

        $password_setup = PasswordSetup::create([
            'email'         => $notifiable->email,
            'token'         => $hashed_token, 
        ]);

        return (new MailMessage)
                    ->subject($project_name)
                    ->line('You are registered as a QMS member. Please change password to complete account setup.')
                    ->action('Change Password', $url)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
