<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiringNotification extends Notification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Subscription $subscription
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tenant = $this->subscription->tenant;
        $plan = $this->subscription->plan;
        $trialEndsAt = $this->subscription->trial_ends_at;

        return (new MailMessage)
            ->subject("O seu trial do plano {$plan->name} termina amanhã")
            ->greeting("Olá {$notifiable->name}!")
            ->line("O período de trial do plano **{$plan->name}** para o tenant **{$tenant->name}** termina amanhã.")
            ->line("**Data de término:** {$trialEndsAt->format('d/m/Y H:i')}")
            ->line("Após o término do trial, o plano continuará ativo mas será cobrado normalmente.")
            ->action('Ver Planos Disponíveis', route('subscriptions.index'))
            ->line('Se pretende fazer downgrade, pode fazê-lo a qualquer momento no painel de controlo.')
            ->salutation('Obrigado por usar o Tenant Manager!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'tenant_id' => $this->subscription->tenant_id,
            'tenant_name' => $this->subscription->tenant->name,
            'plan_name' => $this->subscription->plan->name,
            'trial_ends_at' => $this->subscription->trial_ends_at,
        ];
    }
}
