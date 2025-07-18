## 🔗 PhonePe Integration (Test Mode)

**Initiate Payment:**
- `POST` to `/phonepe-initiate.php`
- Params: `merchantId`, `amount`, `transactionId` (optional)
- Redirects user to PhonePe payment gateway

**Webhook URL:**
- `/webhook.php` handles response from PhonePe

🔒 **Make sure to keep your `Client Secret` private and secure.**
