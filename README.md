# Apirone Crypto Payments for Opencart 3.x #

## Description ##

Accept the most popular cryptocurrencies (BTC, LTC, BCH, Doge etc.) on your store all around the world. Use any crypto supported by provider to accept coins using the Forwarding payment process.

**Key features:**

* Payment automatically forwards from temporarily generated crypto-address directly into your wallet (temp address identify payment to exact order)

* The payment gateway has a fixed fee which does not depend on the amount of the order. Small payments are totally free. [https://apirone.com/pricing](https://apirone.com/pricing)

* You do not need to complete a KYC/Documentation to start using our plugin. Just fill in settings and start your business.

* White label processing (your online store accepts payments on the store side without redirects, iframes, advertisements, logo, etc.)

* This plugin works well all over the world.

* Tor network support.

## Installation ##

1) Download apirone-crypto-payments.ocmod.zip
2) Go to Extensions » Installer and upload apirone-opencart.ocmod.zip
3) Go to Extensions » Extensions. Choose Payments from dropdown menu.
4) Click install button (green plus) Apirone plugin.
5) Click Edit button.
6) Enter your cryptocurrency addresses for desired cryptos and switch plugin Status to enable in Plugin settings.

## How does it work? ##

The Buyer adds items into the cart and prepares the order.
Using API requests, the store generates temporary crypto (BTC, LTC, BCH, Doge) address and show a QR code.
Then, the buyer scans the QR code and pays for the order. This transaction goes to the blockchain.
The payment gateway immediately notifies the store about the payment.
The store completes the transaction.

## Requirements & License ##

Opencart 3.0.3.8

Tested with PHP 7.4

License MIT

## Third Party API & License Information ##

* **API website:-** [https://apirone.com](https://apirone.com)
* **API docs:-** [https://apirone.com/docs/](https://apirone.com/docs/)
* **Privacy policy:-** [https://apirone.com/privacy-policy](https://apirone.com/privacy-policy)
* **Support:-** <support@apirone.com>

## Frequently Asked Questions ##

**I will get money in USD, EUR, CAD, JPY, RUR...?**

>No. You will get crypto only. You can enter the crypto address of your trading platform account and convert crypto (BTC, LTC, BCH, Doge) to fiat money at any time.

**How can The Store cancel orders and return bitcoins?**

>This process is fully manual because you will get all payments to your specified wallet. Only you control your money. Contact the Customer, ask address and finish the deal.
Bitcoin protocol has no refunds, chargebacks, or transaction cancellations.
Only the store manager takes a decision of underpaid or overpaid orders. Cancel and return the rest amount directly to the customers.

**I would like to accept Litecoin only. What should I do?**

> Just enter your LTC address on settings and keep other fields empty.

**Fee:**

>The plugin uses the free Rest API of the Apirone crypto payment gateway. The pricing page [https://apirone.com/pricing](https://apirone.com/pricing)
