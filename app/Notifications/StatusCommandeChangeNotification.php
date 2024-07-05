<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusCommandeChangeNotification extends Notification
{
    use Queueable;

    protected $commande;
    protected $nouveauStatut;

    /**
     * Create a new notification instance.
     */
    public function __construct($commande, $nouveauStatut)
    {
        $this->commande = $commande;
        $this->nouveauStatut = $nouveauStatut;
    }

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
    public function toMail($notifiable)
    {
        $mailMessage = new MailMessage;
        $mailMessage->subject('Mise à jour du statut de votre commande');

        switch ($this->nouveauStatut) {
            case 'confirmation en attente':
                $mailMessage->line('Votre commande a été reçue et est en attente de confirmation.')
                    ->line('Nous vous informerons dès que la commande sera confirmée.');
                break;

            case 'commande confirmée':
                $mailMessage->line('Votre commande a été confirmée.')
                    ->line('Nous préparons votre commande pour l\'expédition.');
                break;

            case 'commande annulée':
                $mailMessage->line('Votre commande a été annulée.')
                    ->line('Si vous avez des questions, veuillez nous contacter.');
                break;

            case 'en cours de livraison':
                $mailMessage->line('Votre commande est en cours de livraison.')
                    ->line('Vous recevrez votre commande bientôt.');
                break;

            case 'commande livrée':
                $mailMessage->line('Votre commande a été livrée.')
                    ->line('Merci pour votre achat.');
                break;

            default:
                $mailMessage->line('Le statut de votre commande a changé.');
                break;
        }

        $mailMessage->action('Voir ma commande', url('/commandes/'.$this->commande->id))
            ->line('Merci pour votre confiance !');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable)
    {
        return [
            'commande_id' => $this->commande->id,
            'nouveau_statut' => $this->nouveauStatut,
        ];
    }
}
