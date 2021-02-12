<?php
/*********************************************************************************
 * This file is part of Myddleware.

 * @package Myddleware
 * @copyright Copyright (C) 2013 - 2015  Stéphane Faure - CRMconsult EURL
 * @copyright Copyright (C) 2015 - 2016  Stéphane Faure - Myddleware ltd - contact@myddleware.com
 * @link http://www.myddleware.com

 This file is part of Myddleware.

 Myddleware is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Myddleware is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Myddleware.  If not, see <http://www.gnu.org/licenses/>.
*********************************************************************************/

$moduleFields = [
    'call-log' => [
        'id' => ['label' => 'id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'uri' => ['label' => 'uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'sessionId' => ['label' => 'Session Id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__phoneNumber' => ['label' => 'From phone number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__extensionNumber' => ['label' => 'From extension number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__location' => ['label' => 'From location', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__name' => ['label' => 'From name', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        // 'from__device__id' => array('label' => 'From device id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        // 'from__device__uri' => array('label' => 'From device uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        'to__phoneNumber' => ['label' => 'To phone number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'to__extensionNumber' => ['label' => 'To extension number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'to__location' => ['label' => 'To location', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'to__name' => ['label' => 'To name', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        // 'to__device__id' => array('label' => 'To device id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        // 'to__device__uri' => array('label' => 'To device uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        'type' => ['label' => 'Type', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Voice' => 'Voice', 'Fax' => 'Fax']],
        'direction' => ['label' => 'Direction', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Inbound' => 'Inbound', 'Outbound' => 'Outbound']],
        'action' => ['label' => 'Action', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => [
            'Unknown' => 'Unknown',
            'Phone Call' => 'Phone Call',
            'Phone Login' => 'Phone Login',
            'Incoming Fax' => 'Incoming Fax',
            'Accept Call' => 'Accept Call',
            'FindMe' => 'FindMe',
            'FollowMe' => 'FollowMe',
            'Outgoing Fax' => 'Outgoing Fax',
            'Call Return' => 'Call Return',
            'Calling Card' => 'Calling Card',
            'Ring Directly' => 'Ring Directly',
            'RingOut Web' => 'RingOut Web',
            'VoIP Call' => 'VoIP Call',
            'RingOut PC' => 'RingOut PC',
            'RingMe' => 'RingMe',
            'Transfer' => 'Transfer',
            '411 Info' => '411 Info',
            'Emergency' => 'Emergency',
            'E911 Update' => 'E911 Update',
            'Support' => 'Support',
            'RingOut Mobile' => 'RingOut Mobile',
        ]],
        'result' => ['label' => 'result', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => [
            'Unknown' => 'Unknown',
            'ResultInProgress' => 'ResultInProgress',
            'Missed' => 'Missed',
            'Call accepted' => 'Call accepted',
            'Voicemail' => 'Voicemail',
            'Rejected' => 'Rejected',
            'Reply' => 'Reply',
            'Received' => 'Received',
            'Receive Error' => 'Receive Error',
            'Fax on Demand' => 'Fax on Demand',
            'Partial Receive' => 'Partial Receive',
            'Blocked' => 'Blocked',
            'Call connected' => 'Call connected',
            'No Answer' => 'No Answer',
            'International Disabled' => 'International Disabled',
            'Busy' => 'Busy',
            'Send Error' => 'Send Error',
            'Sent' => 'Sent',
            'No fax machine' => 'No fax machine',
            'ResultEmpty' => 'ResultEmpty',
            'Account ' => 'Account ',
            'Suspended' => 'Suspended',
            'Call Failed' => 'Call Failed',
            'Call Failure' => 'Call Failure',
            'Internal Error' => 'Internal Error',
            'IP Phone offline' => 'IP Phone offline',
            'Restricted Number' => 'Restricted Number',
            'Wrong Number' => 'Wrong Number',
            'Stopped' => 'Stopped',
            'Hang up' => 'Hang up',
            'Poor Line Quality' => 'Poor Line Quality',
            'Partially Sent' => 'Partially Sent',
            'International Restriction' => 'International Restriction',
            'Abandoned' => 'Abandoned',
            'Declined' => 'Declined',
            'Fax Receipt Error' => 'Fax Receipt Error',
            'Fax Send Error' => 'Fax Send Error',
        ]],
        'startTime' => ['label' => 'Start Time', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'duration' => ['label' => 'Duration', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'recording__id' => ['label' => 'Recording id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'recording__uri' => ['label' => 'Recording uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'recording__type' => ['label' => 'Recording type', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Automatic' => 'Automatic', 'OnDemand' => 'OnDemand']],
        'recording__contentUri' => ['label' => 'Recording contentUri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'lastModifiedTime' => ['label' => 'Last modified time', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        // 'legs' => array('label' => 'Last modified time', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
    ],
    'message-store' => [
        'id' => ['label' => 'Id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'uri' => ['label' => 'Uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        // 'attachments__' => array('label' => 'attachments', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        'availability' => ['label' => 'Availability', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Alive' => 'Alive', 'Deleted' => 'Deleted', 'Purged' => 'Purged']],
        // 'conversationId' => array('label' => 'conversationId', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        // 'conversation' => array('label' => 'conversation', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        'creationTime' => ['label' => 'Creation time', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'deliveryErrorCode' => ['label' => 'Delivery error code', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'direction' => ['label' => 'Direction', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Inbound' => 'Inbound', 'Outbound' => 'Outbound']],
        'faxPageCount' => ['label' => 'Fax page count', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'faxResolution' => ['label' => 'Fax resolution', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['High' => 'High', 'Low' => 'Low']],
        'coverIndex' => ['label' => 'Cover index', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'coverPageText' => ['label' => 'Cover page text', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__phoneNumber' => ['label' => 'From phone number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__extensionNumber' => ['label' => 'From extension number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__location' => ['label' => 'From location', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'from__name' => ['label' => 'From name', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        // 'from__device__id' => array('label' => 'From device id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        // 'from__device__uri' => array('label' => 'From device uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        'to__phoneNumber' => ['label' => 'To phone number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'to__extensionNumber' => ['label' => 'To extension number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'to__location' => ['label' => 'To location', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'to__name' => ['label' => 'To name', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        // 'to__device__id' => array('label' => 'To device id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        // 'to__device__uri' => array('label' => 'To device uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0),
        'lastModifiedTime' => ['label' => 'Last modified time', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'messageStatus' => ['label' => 'Message status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Queued' => 'Queued', 'Sent' => 'Sent', 'SendingFailed' => 'SendingFailed', 'Delivered' => 'Delivered', 'DeliveryFailed' => 'DeliveryFailed', 'Received' => 'Received']],
        'pgToDepartment' => ['label' => 'Pg to department', 'type' => 'boolean', 'type_bdd' => 'boolean', 'required' => 0, 'option' => ['Fax' => 'Fax', 'SMS' => 'SMS', 'VoiceMail' => 'VoiceMail', 'Pager' => 'Pager', 'Text' => 'Text']],
        'priority' => ['label' => 'Priority', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['High' => 'High', 'Normal' => 'Normal']],
        'readStatus' => ['label' => 'Read status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Read' => 'Read', 'Unread' => 'Unread']],
        'smsDeliveryTime' => ['label' => 'SMS delivery time', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'smsSendingAttemptsCount' => ['label' => 'SMS sending attempts count', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'subject' => ['label' => 'Subject', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'type' => ['label' => 'Type', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Fax' => 'Fax', 'SMS' => 'SMS', 'VoiceMail' => 'VoiceMail', 'Pager' => 'Pager', 'Text' => 'Text']],
        'vmTranscriptionStatus' => ['label' => 'VM transcription status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['NotAvailable' => 'NotAvailable', 'InProgress' => 'InProgress', 'TimedOut' => 'TimedOut', 'Completed' => 'Completed', 'CompletedPartially' => 'CompletedPartially', 'Failed' => 'Failed']],
    ],
    'presence' => [
        'id' => ['label' => 'Id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'uri' => ['label' => 'Uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'dndStatus' => ['label' => 'Do not Disturb status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['TakeAllCalls' => 'TakeAllCalls', 'DoNotAcceptAnyCalls' => 'DoNotAcceptAnyCalls', 'DoNotAcceptDepartmentCalls' => 'DoNotAcceptDepartmentCalls', 'TakeDepartmentCallsOnly' => 'TakeDepartmentCallsOnly']],
        'extension__id' => ['label' => 'Extension id', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'extension__uri' => ['label' => 'Extension uri', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'extension__extensionNumber' => ['label' => 'Extension Number', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'message' => ['label' => 'Message', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'presenceStatus' => ['label' => 'Presence Status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Offline' => 'Offline', 'Busy' => 'Busy', 'Available' => 'Available']],
        'allowSeeMyPresence' => ['label' => 'Allow See My Presence', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'pickUpCallsOnHold' => ['label' => 'Pick Up Calls On Hold', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'ringOnMonitoredCall' => ['label' => 'Ring On Monitored Call', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0],
        'telephonyStatus' => ['label' => 'Telephony status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['NoCall' => 'NoCall', 'CallConnected' => 'CallConnected', 'Ringing' => 'Ringing', 'OnHold' => 'OnHold', 'ParkedCall' => 'ParkedCall']],
        'userStatus' => ['label' => 'User status', 'type' => 'string', 'type_bdd' => 'string', 'required' => 0, 'option' => ['Offline' => 'Offline', 'Busy' => 'Busy', 'Available' => 'Available']],
    ],
];

// Metadata override if needed
$file = __DIR__.'/../../../Custom/Solutions/lib/ringcentral/metadata.php';
if (file_exists($file)) {
    require_once $file;
}
