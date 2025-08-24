
## Project setup

**Prerequisites**: Git, Docker, PHP, Composer

1. Clone this git repository
```
git@github.com:TopiasM/smindle-assignment.git
```
2. Move to the project folder
```
cd smindle-assignment
```
3. Execute setup shell script, which runs composer install, traefik & sail containers, and Laravel migrations & queue.
<br /><sup>(Uses network called mock and subnet 192.168.200.0/24. If these can't be used some changes need to be made)</sup>
```
./setup
```

## Request example

**POST** /api/v1/order

JSON body
```
{
    "client": {
        "identity": "Alan Turing",
        "contact_point": "123 Enigma Ave, Bletchley Park, UK"
    },
    "contents": [
        {
            "label": "Smindle ElePHPant plushie",
            "kind": "single",
            "cost": 295.45,
            "meta": {}
        },
        {
            "label": "Syntax & Chill",
            "kind": "recurring",
            "cost": 175.00,
            "meta": {
                "frequency": "unspecified",
                "priority": "high"
            }
        }
    ]
}
```

Response (201 Created)
```
{
    "status": 1,
    "message": "Order created successfully",
    "order": {
        "client_identity": "Alan Turing",
        "client_address": "123 Enigma Ave, Bletchley Park",
        "client_country": "UK",
        "order_number": "UK8",
        "updated_at": "2025-08-24T21:14:41.000000Z",
        "created_at": "2025-08-24T21:14:41.000000Z",
        "id": 8,
        "order_contents": [
            {
                "id": 15,
                "order_id": 8,
                "order_number": "UK8",
                "label": "Smindle ElePHPant plushie",
                "kind": "single",
                "cost": 295.45,
                "metadata": "[]",
                "created_at": "2025-08-24T21:14:42.000000Z",
                "updated_at": "2025-08-24T21:14:42.000000Z"
            },
            {
                "id": 16,
                "order_id": 8,
                "order_number": "UK8",
                "label": "Syntax & Chill",
                "kind": "recurring",
                "cost": 175,
                "metadata": "{\"priority\": \"high\", \"frequency\": \"unspecified\"}",
                "created_at": "2025-08-24T21:14:42.000000Z",
                "updated_at": "2025-08-24T21:14:42.000000Z"
            }
        ]
    }
}
```
## My notes
- Used traefik to create very-slow-api.test domain with https
  - Set fixed ip address: 192.168.200.3 for treafik, so the laravel application can connect to it
  - Used https://github.com/dipaksarkar/laravel-sail-with-traefik as a starting point
  - Reroutes localhost/api/mock/orders -> https://very-slow-api.test/orders
- Mysql and Redis used
- All data is validated
    - Expects contact_point to be an address + country
- Installed laravel octane to allow more concurrent requests (Sail only supported one)
- Queue jobs is used for handling the recurring items
    - meta.frequency is used to assign the queue(high/default/low)
- Created Idempotency middleware to check if duplicate requests are sent
    - Used request to create the key, but usually this would be something that client would sent to the server
    - 10 seconds timeout for same request bodys
- Info about very-slow-api calls is stored in the api_calls table
