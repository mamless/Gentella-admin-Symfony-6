<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class ForcePasswordChange implements EventSubscriberInterface
{


    public function __construct(private Security $security, private UrlGeneratorInterface $urlGenerator )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequestE'
        ];
    }

    public function onKernelRequestE(RequestEvent $event):void
    {
        // only deal with the main request, disregard subrequests
        if (!$event->isMainRequest()) {
            return;
        }

        // if we are visiting the password change route, no need to redirect
        if ($event->getRequest()->attributes->get('_route') == 'app_admin_changepswd') {
            return;
        }

        $user = $this->security->getUser();
        // if you do not have a valid user, it means it's not an authenticated request, so it's not our concern
        if (!$user instanceof User) {
            return;
        }


        // if it's not their first login, and have is password changed = true, no need to redirect
        if (!$user->isInitMdp()) {
            return;
        }

        // if we get here, it means we need to redirect them to the password change view.
        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_admin_changepswd')));



    }
}