<?php
/**
 * @file plugins/generic/welcomeEmail/WelcomeEmailPlugin.inc.php
 *
 * Copyright (c) 2026 OJS Services
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class WelcomeEmailPlugin
 * @brief Sends a welcome email to users after self-registration without requiring activation.
 *
 * @author Kerim Sarıgül <info@ojs-services.com>
 * @version 1.0.0
 * @date 2026-01-17
 * @link https://github.com/kerimsarigul
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class WelcomeEmailPlugin extends GenericPlugin {

    /**
     * @copydoc Plugin::register()
     */
    public function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {
            HookRegistry::register('registrationform::execute', [$this, 'onRegistrationExecute']);
        }
        return $success;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName() {
        return __('plugins.generic.welcomeEmail.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription() {
        return __('plugins.generic.welcomeEmail.description');
    }

    /**
     * Hook callback: Called when registration form is executed
     * @param string $hookName
     * @param array $args
     * @return bool
     */
    public function onRegistrationExecute($hookName, $args) {
        $form = $args[0] ?? null;
        
        if (!$form) {
            return false;
        }

        $user = $form->user ?? null;
        
        if (!$user) {
            return false;
        }

        // Get email
        $email = $user->getData('email');
        if (empty($email)) {
            $email = $user->getEmail();
        }
        
        if (empty($email)) {
            return false;
        }

        // Get user name
        $fullName = $this->getUserFullName($user);

        // Get context info
        $request = Application::get()->getRequest();
        $context = $request ? $request->getContext() : null;
        
        $siteName = 'Our Journal';
        $siteUrl = '';
        
        if ($context) {
            $siteName = $context->getLocalizedName();
            $siteUrl = $request->getDispatcher()->url($request, ROUTE_PAGE, $context->getPath());
        } else {
            $site = $request->getSite();
            if ($site) {
                $siteName = $site->getLocalizedTitle();
                $siteUrl = $request->getBaseUrl();
            }
        }

        // Send email
        $this->sendWelcomeEmail($email, $fullName, $siteName, $siteUrl, $context, $request);

        return false;
    }

    /**
     * Get user's full name from user object
     * @param object $user
     * @return string
     */
    private function getUserFullName($user) {
        $givenName = $user->getData('givenName');
        $familyName = $user->getData('familyName');
        
        if (is_array($givenName)) {
            $givenName = reset($givenName);
        }
        if (is_array($familyName)) {
            $familyName = reset($familyName);
        }
        
        $fullName = trim($givenName . ' ' . $familyName);
        
        if (empty($fullName) && method_exists($user, 'getFullName')) {
            $fullName = $user->getFullName();
        }
        
        return $fullName ?: 'User';
    }

    /**
     * Send welcome email to user
     * @param string $toEmail
     * @param string $toName
     * @param string $siteName
     * @param string $siteUrl
     * @param object|null $context
     * @param object $request
     * @return bool
     */
    private function sendWelcomeEmail($toEmail, $toName, $siteName, $siteUrl, $context, $request) {
        import('lib.pkp.classes.mail.Mail');
        
        $mail = new Mail();
        
        // Get sender info
        $fromEmail = '';
        $fromName = $siteName;
        
        if ($context) {
            $fromEmail = $context->getData('contactEmail');
            $fromName = $context->getData('contactName') ?: $siteName;
        }
        
        if (empty($fromEmail) && $request) {
            $site = $request->getSite();
            if ($site) {
                $fromEmail = $site->getLocalizedContactEmail();
                $fromName = $site->getLocalizedContactName() ?: $siteName;
            }
        }

        if (empty($fromEmail)) {
            error_log('WelcomeEmailPlugin: From email not configured');
            return false;
        }

        $mail->setFrom($fromEmail, $fromName);
        $mail->setRecipients([
            [
                'name' => $toName,
                'email' => $toEmail
            ]
        ]);
        
        $subject = __('plugins.generic.welcomeEmail.emailSubject', ['journalName' => $siteName]);
        $mail->setSubject($subject);
        
        // Build HTML email body
        $body = $this->buildEmailBody($toName, $siteName, $siteUrl);
        
        $mail->setBody($body);
        $mail->setContentType('text/html');

        try {
            $result = $mail->send();
            if ($result) {
                error_log('WelcomeEmailPlugin: Email sent successfully to ' . $toEmail);
            } else {
                error_log('WelcomeEmailPlugin: Failed to send email to ' . $toEmail);
            }
            return $result;
        } catch (Exception $e) {
            error_log('WelcomeEmailPlugin: Exception - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build HTML email body
     * @param string $toName
     * @param string $siteName
     * @param string $siteUrl
     * @return string
     */
    private function buildEmailBody($toName, $siteName, $siteUrl) {
        $greeting = __('plugins.generic.welcomeEmail.emailGreeting', ['userName' => htmlspecialchars($toName)]);
        $thankYou = __('plugins.generic.welcomeEmail.emailThankYou', ['journalName' => htmlspecialchars($siteName)]);
        $accountCreated = __('plugins.generic.welcomeEmail.emailAccountCreated');
        $visitButton = __('plugins.generic.welcomeEmail.emailVisitButton');
        $regards = __('plugins.generic.welcomeEmail.emailRegards');
        $autoMessage = __('plugins.generic.welcomeEmail.emailAutoMessage');
        
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #333333; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4;">
        <tr>
            <td style="padding: 20px 0;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #2c3e50; padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: normal;">' . htmlspecialchars($siteName) . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px;">' . $greeting . '</p>
                            <p style="margin: 0 0 20px 0;">' . $thankYou . '</p>
                            <p style="margin: 0 0 20px 0;">' . $accountCreated . '</p>
                            ' . ($siteUrl ? '<p style="margin: 0 0 30px 0; text-align: center;">
                                <a href="' . htmlspecialchars($siteUrl) . '" style="display: inline-block; background-color: #3498db; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-weight: bold;">' . $visitButton . '</a>
                            </p>' : '') . '
                            <p style="margin: 0 0 10px 0;">' . $regards . '</p>
                            <p style="margin: 0; font-weight: bold;">' . htmlspecialchars($siteName) . '</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ecf0f1; padding: 20px 40px; text-align: center; font-size: 12px; color: #7f8c8d;">
                            <p style="margin: 0;">' . $autoMessage . '</p>
                            ' . ($siteUrl ? '<p style="margin: 10px 0 0 0;"><a href="' . htmlspecialchars($siteUrl) . '" style="color: #3498db; text-decoration: none;">' . htmlspecialchars($siteName) . '</a></p>' : '') . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
}
