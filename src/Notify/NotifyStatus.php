<?php
namespace Payu\Notify;

abstract class NotifyStatus
{
    // Dla powiadomień
    const STATUS_WAITING_FOR_CONFIRMATION = 'WAITING_FOR_CONFIRMATION';
    const STATUS_PENDING = 'PENDING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELED = 'CANCELED';
    // Inne
    const STATUS_NEW = 'NEW';
	const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_CANCEL = 'CANCEL';
    const STATUS_FINALIZED = 'FINALIZED';

    // Allowed status
    const STATUS_ALL = ['NEW', 'CANCEL', 'CANCELED', 'SUCCESS', 'PENDING', 'WAITING_FOR_CONFIRMATION', 'COMPLETED', 'FINALIZED'];

    // Gateway
    const GATEWAY_PAYU = 'PAYU';
}