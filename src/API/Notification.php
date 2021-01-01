<?php

namespace NNV\OneSignal\API;

use NNV\OneSignal\OneSignal;
use NNV\OneSignal\Utils\Validation;
use Symfony\Component\OptionsResolver\Options;

/**
 * Notification API
 */
class Notification
{
    /**
     * OneSignal instance
     *
     * @var \NNV\OneSignal\OneSignal
     */
    private $oneSignal;

    /**
     * Application ID
     *
     * @var string
     */
    private $appIDKey;

    /**
     * RESTful API Key
     *
     * @var string
     */
    private $restAPIKey;

    /**
     * Extra options
     *
     * @var array
     */
    private $extraOptions;

    /**
     * @param \NNV\OneSignal\OneSignal $oneSignal OneSignal instance
     * @param string $appIDKey Application ID
     * @param string $restAPIKey API Key for REST JSON API
     */
    public function __construct(OneSignal $oneSignal, $appIDKey = null, $restAPIKey = null)
    {
        $this->oneSignal    = $oneSignal;
        $this->appIDKey     = ($appIDKey ? $appIDKey : $oneSignal->getAppIDKey());
        $this->restAPIKey   = ($restAPIKey ? $restAPIKey : $oneSignal->getRESTAPIKey());
        $this->extraOptions = [
            'headers' => [
                'Authorization' => sprintf('Basic %s', $this->restAPIKey),
            ]
        ];
    }

    /**
     * Sends notifications to users
     *
     * @param  array  $notificationData Notification data
     * @return \NNV\OneSignal\OneSignal::execute()
     */
    public function create(array $notificationData)
    {
        $notificationData = array_merge($notificationData, [
            'app_id' => $this->appIDKey,
        ]);
        $notificationData = $this->validateNotificationData(
            $notificationData,
            $this->getNotificationDataRules()
        );

        return $this->oneSignal->execute('notifications', 'POST', array_merge([
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $notificationData,
        ], $this->extraOptions));
    }

    /**
     * Stop a scheduled or currently outgoing notification
     *
     * @param  string $notificationID Notification ID
     * @return \NNV\OneSignal\OneSignal::execute()
     */
    public function cancel($notificationID)
    {
        $url = sprintf('notifications/%s?app_id=%s', $notificationID, $this->appIDKey);

        return $this->oneSignal->execute($url, 'DELETE', $this->extraOptions);
    }

    /**
     * View the details of a single notification
     *
     * @param  string $notificationID Notification ID
     * @return \NNV\OneSignal\OneSignal::execute()
     */
    public function get($notificationID)
    {
        $url = sprintf('notifications/%s?app_id=%s', $notificationID, $this->appIDKey);

        return $this->oneSignal->execute($url, 'GET', $this->extraOptions);
    }

    /**
     * View the details of multiple notifications
     *
     * @param  integer $limit  How many notifications to return.
     * @param  integer $offset Result offset. Result are sorted by queued_at
     * @return \NNV\OneSignal\OneSignal::execute()
     */
    public function all($limit = 10, $offset = 0)
    {
        $url = sprintf(
            'notifications?app_id=%s&limit=%s&offset=%s',
            $this->appIDKey,
            $limit,
            $offset
        );

        return $this->oneSignal->execute($url, 'GET', $this->extraOptions);
    }

    /**
     * Track when users open a notification
     *
     * @param  string $notificationID Notification ID
     * @return \NNV\OneSignal\OneSignal::execute()
     */
    public function trackOpen($notificationID)
    {
        $url = sprintf('notifications/%s', $notificationID);
        $trackOpenData = [
            'app_id' => $this->appIDKey,
            'opened' => true,
        ];

        return $this->oneSignal->execute($url, 'PUT', array_merge([
            'form_params' => $trackOpenData,
        ], $this->extraOptions));
    }

    /**
     * Notification data validation rules
     *
     * @return array Validation rules
     */
    private function getNotificationDataRules()
    {
        return [
            'required' => ['app_id'],
            'defined' => [
                'app_id' => 'string',
                'app_ids' => 'array',
                'included_segments' => 'array',
                'excluded_segments' => 'array',
                'filters' => 'array',
                'include_player_ids' => 'array',
                'include_ios_tokens' => 'array',
                'include_wp_urls' => 'array',
                'include_wp_wns_uris' => 'array',
                'include_amazon_reg_ids' => 'array',
                'include_chrome_reg_ids' => 'array',
                'include_chrome_web_reg_ids' => 'array',
                'include_android_reg_ids' => 'array',
                'contents' => 'array',
                'headings' => 'array',
                'subtitle' => 'array',
                'template_id' => 'string',
                'content_available' => 'bool',
                'mutable_content' => 'bool',
                'data' => 'array',
                'url' => 'string',
                'ios_attachments' => 'array',
                'big_picture' => 'string',
                'adm_big_picture' => 'string',
                'chrome_big_picture' => 'string',
                'buttons' => 'array',
                'web_buttons' => 'array',
                'ios_category' => 'string',
                'android_background_layout' => 'array',
                'small_icon' => 'string',
                'large_icon' => 'string',
                'chrome_web_icon' => 'string',
                'firefox_icon' => 'string',
                'adm_small_icon' => 'string',
                'adm_large_icon' => 'string',
                'chrome_icon' => 'string',
                'ios_sound' => 'string',
                'android_sound' => 'string',
                'adm_sound' => 'string',
                'wp_sound' => 'string',
                'wp_wns_sound' => 'string',
                'android_led_color' => 'string',
                'android_accent_color' => 'string',
                'android_visibility' => [
                    'allowedTypes' => 'int',
                    'allowedValues' => [1, 0, -1],
                ],
                'ios_badgeType' => [
                    'allowedTypes' => 'string',
                    'allowedValues' => ['None', 'SetTo', 'Increase'],
                ],
                'ios_badgeCount' => 'string',
                'collapse_id' => 'string',
                'send_after' => 'string',
                'delayed_option' => 'string',
                'delivery_time_of_day' => 'string',
                'ttl' => 'int',
                'priority' => 'int',
                'android_group' => 'string',
                'android_group_message' => 'array',
                'adm_group' => 'string',
                'adm_group_message' => 'array',
                'isIos' => 'bool',
                'isAndroid' => 'bool',
                'isAnyWeb' => 'bool',
                'isChromeWeb' => 'bool',
                'isFirefox' => 'bool',
                'isSafari' => 'bool',
                'isWP' => 'bool',
                'isWP_WNS' => 'bool',
                'isAdm' => 'bool',
                'isChrome' => 'bool',
                'android_channel_id' => 'string',
                'huawei_channel_id' => 'string',
            ],
        ];
    }

    /**
     * Validate notification data
     *
     * @param  array  $notificationData      Notification data
     * @param  array  $notificationDataRules Notification data validation rules
     * @return mixed  NNV\OneSignal\Utils\Validation::validate or data resolved
     */
    private function validateNotificationData(array $notificationData, array $notificationDataRules)
    {
        $validation = new Validation;
        $isObjectOptions = [
            'contents', 'headings', 'subtitle', 'data',
            'ios_attachments', 'android_background_layout',
            'android_group_message', 'adm_group_message', 'buttons',
            'web_buttons', 'filters',
        ];

        $validation->setMultiRequired($notificationDataRules['required'])
                   ->setMultiDefined($notificationDataRules['defined'])
                   ->setAllowedValues('send_after', function($sendAfter) {
                        try {
                            new \DateTime($sendAfter);
                        } catch (\Exception $e) {
                            return false;
                        }

                        return true;
                   })
                   ->setAllowedValues('delivery_time_of_day', function($deliveryTimeOfDay) {
                        return preg_match('/^(0?\d|1[0-2]):[0-5]\d(am|pm)$/i', $deliveryTimeOfDay);
                   });

        foreach ($isObjectOptions as $objectOption) {
            $validation->setNormalizer($objectOption, function(Options $options, $objectValue) {
                return json_decode(json_encode($objectValue));
            });
        }

        return $validation->validate($notificationData);
    }
}
