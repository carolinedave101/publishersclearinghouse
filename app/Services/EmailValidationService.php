<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EmailValidationService
{
    private array $disposableDomains = [
        '10minutemail.com', '10minutenail.com', '10x9.com', '33mail.com',
        'anonaddy.com', 'anonymousemail.com', 'boun.cr', 'brefmail.com',
        'burnermail.io', 'byom.de', 'cloud-mail.top', 'cmail.com',
        'crapmail.org', 'cuoly.com', 'demail.com', 'discard.email',
        'dispostable.com', 'emailfake.com', 'emailnaj.com', 'emailondeck.com',
        'emaillime.com', 'emails.ga', 'emailtemp.com', 'emailthe.net',
        'emailtmp.com', 'fakeinbox.com', 'fakemail.com', 'fakemailgenerator.com',
        'fakemail.net', 'garrymccooey.com', 'geekforex.com', 'generator.email',
        'getnada.com', 'gettempmail.com', 'guerrillamail.com', 'guerrillamail.org',
        'guerrillamail.net', 'gurek.net', 'haltospam.com', 'hotmailproduct.com',
        'inboxbear.com', 'inboxkitten.com', 'irssi.tv', 'jetable.com',
        'kein.org', 'killmail.com', 'killmail.net', 'linkha2.com',
        'lolfreemoney.com', 'lookugly.com', 'lukop.dk', 'mailexpire.com',
        'mailforspam.com', 'mailfreeonline.com', 'mailin8r.com', 'mailinator.com',
        'mailinator.org', 'mailinator.net', 'mailme.icu', 'mailmetrash.com',
        'mailnator.com', 'mailprotech.com', 'mailsac.com', 'mailsdump.com',
        'mailspam.xyz', 'mailtemp.org', 'mailtester.com', 'mailthrow.com',
        'mailtothis.com', 'maozed.top', 'mintemail.com', 'moakt.com',
        'mohmal.com', 'mytrashmail.com', 'negated.com', 'netmails.com',
        'neverbox.com', 'noob.com', 'nospam.gg', 'nwldx.com',
        'oneoffmail.com', 'opayq.com', 'pookmail.com', 'protonmail.com',
        'quickmail.com', 'rcpt.at', 'receiveee.com', 're-gister.com',
        'safetymail.info', 'sandelf.de', 'sastamas.com', 'sharklasers.com',
        'shiftmail.com', 'slipsum.com', 'sneakemail.com', 'snkmail.com',
        'spam.la', 'spam4.me', 'spambob.com', 'spambox.me',
        'spamcero.com', 'spamdecoy.net', 'spamex.com', 'spamgourmet.com',
        'spamherelots.com', 'spamhole.com', 'spamify.com', 'spaminator.de',
        'spamkill.info', 'spaml.com', 'spamobox.com', 'spamslicer.com',
        'spamspot.com', 'spamthis.co.uk', 'spamtrail.com', 'spamtroll.net',
        'spamwc.de', 'speed.1s.fr', 'suioe.com', 'teewars.org',
        'temp-mail.org', 'tempail.com', 'tempemail.com', 'tempemail.net',
        'temporarymail.com', 'tempmail.ninja', 'tempmail1.com', 'tempr.email',
        'thankyou2010.com', 'thc.st', 'throwaway.email', 'trash2009.com',
        'trashmail.com', 'trashmail.org', 'trashmail.net', 'trashymail.com',
        'turual.com', 'tyldd.com', 'uggsrock.com', 'wegwerfmail.de',
        'wegwerfmail.net', 'wegwerfmail.org', 'wh4f.org', 'whyspam.me',
        'willselfdestruct.com', 'winemaven.info', 'wronghead.com', 'wuzup.net',
        'xagloo.com', 'xkx.me', 'xoxy.net', 'yep.it',
        'yopmail.com', 'yopmail.fr', 'yopmail.net', 'yuurok.com',
        'zehnminutenmail.de', 'zippymail.info', 'zoaxe.com', 'zomg.info',
    ];

    private array $commonTypoDomains = [
        'gmial.com' => 'gmail.com',
        'gmil.com' => 'gmail.com',
        'gmal.com' => 'gmail.com',
        'gmai.com' => 'gmail.com',
        'gnail.com' => 'gmail.com',
        'gmaill.com' => 'gmail.com',
        'gmaik.com' => 'gmail.com',
        'gamil.com' => 'gmail.com',
        'yaho.com' => 'yahoo.com',
        'yhoo.com' => 'yahoo.com',
        'yahooo.com' => 'yahoo.com',
        'yahho.com' => 'yahoo.com',
        'yahim.com' => 'yahoo.com',
        'yaim.com' => 'yahoo.com',
        'hotmai.com' => 'hotmail.com',
        'hotmal.com' => 'hotmail.com',
        'hotmial.com' => 'hotmail.com',
        'homtail.com' => 'hotmail.com',
        'hotmali.com' => 'hotmail.com',
        'htomail.com' => 'hotmail.com',
        'outlok.com' => 'outlook.com',
        'outllok.com' => 'outlook.com',
        'outloo.com' => 'outlook.com',
        'aol.co' => 'aol.com',
        'aol.cm' => 'aol.com',
    ];

    public function isDeliverable(string $email): array
    {
        $email = trim($email);

        if (empty($email)) {
            return $this->invalid('Empty email address');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->invalid('Invalid email format');
        }

        if (strlen($email) > 254) {
            return $this->invalid('Email too long');
        }

        $domain = substr(strrchr($email, '@'), 1);
        if (empty($domain)) {
            return $this->invalid('No domain found');
        }

        $domain = strtolower($domain);

        if (in_array($domain, $this->disposableDomains, true)) {
            return $this->invalid('Disposable email domain not allowed');
        }

        if (isset($this->commonTypoDomains[$domain])) {
            return $this->invalid(
                'Likely typo: did you mean ' . $this->commonTypoDomains[$domain] . '?'
            );
        }

        $dnsCheck = $this->checkDns($domain);
        if (!$dnsCheck['valid']) {
            return $this->invalid($dnsCheck['reason']);
        }

        return $this->valid();
    }

    private function checkDns(string $domain): array
    {
        if (checkdnsrr($domain, 'MX')) {
            return ['valid' => true];
        }

        if (checkdnsrr($domain, 'A')) {
            return ['valid' => true];
        }

        if (checkdnsrr($domain, 'AAAA')) {
            return ['valid' => true];
        }

        if (checkdnsrr($domain, 'CNAME')) {
            return ['valid' => true];
        }

        $hasAny = false;
        foreach (['MX', 'A', 'AAAA', 'CNAME', 'NS'] as $type) {
            if (@checkdnsrr($domain, $type)) {
                $hasAny = true;
                break;
            }
        }

        if (!$hasAny) {
            return ['valid' => false, 'reason' => "Domain {$domain} has no DNS records"];
        }

        return ['valid' => false, 'reason' => "Domain {$domain} has no mail servers (no MX/A records)"];
    }

    public static function isHardBounce(string $errorMessage): bool
    {
        $msg = strtolower($errorMessage);

        $hardPatterns = [
            'mailbox not found',
            'mailbox not exist',
            'mailbox unavailable',
            'user unknown',
            'no such recipient',
            'no such user',
            'does not exist',
            'not exist',
            'invalid address',
            'invalid email',
            'invalid mailbox',
            'address rejected',
            'recipient rejected',
            'undeliverable',
            'account disabled',
            'account not found',
            'account has been disabled',
            'account does not exist',
            'banned',
            'permanent failure',
            'permanently rejected',
            'permanent error',
            'no mailbox',
            'domain not found',
            'domain does not exist',
            'name service error',
            'dns lookup failed',
            'domain not resolved',
            'bad destination mailbox',
            '5.1.1',
            '5.1.2',
            '5.1.3',
            '5.1.6',
            '5.4.4',
            '5.4.1',
            '550 5.1.1',
            '550 5.1.2',
            '550 5.1.6',
            '553 5.1.3',
            '554 5.1.1',
            '550 5.4.1',
            'recipient address rejected',
            'mailbox name invalid',
        ];

        foreach ($hardPatterns as $pattern) {
            if (str_contains($msg, $pattern)) {
                return true;
            }
        }

        if (preg_match('/\b55[0-9]\b/', $msg) && !str_contains($msg, '4.') && !str_contains($msg, 'try again')) {
            return true;
        }

        return false;
    }

    private function valid(): array
    {
        return ['valid' => true, 'reason' => null, 'check' => 'pass'];
    }

    private function invalid(string $reason): array
    {
        return ['valid' => false, 'reason' => $reason, 'check' => 'fail'];
    }
}