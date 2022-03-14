# Example of Subscribe

```
<type name="Mirasvit\Mq\Api\Repository\ConsumerRepositoryInterface">
    <arguments>
        <argument name="consumers" xsi:type="array">
            <item name="notificator" xsi:type="array">
                <item name="queue" xsi:type="string">mirasvit.event.register</item>
                <item name="callback" xsi:type="string">Mirasvit\Notificator\Api\Service\NotificationServiceInterface::handleEvent</item>
            </item>
        </argument>
    </arguments>
</type>
```