# Ecommerce APIs

A RESTful API built with Laravel for an e-commerce platform, featuring user authentication, product management, shopping cart, and order processing.

## Postman Collection Link 
https://www.postman.com/meltwansy/public/collection/29812851-2322a7ad-ec60-4029-b487-928644b77235?action=share&creator=29812851

## Features

- **Authentication**: User signup and signin with Laravel Sanctum
- **Product Management**: Browse and view products
- **Shopping Cart**: Add, Show, and remove items from cart
- **Order Management**: Create and view orders
- **Database Transactions**: Atomic operations for order creation with stock validation
- **Caching**: Product cache invalidation for real-time updates

## Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (API tokens)
- **ORM**: Eloquent
- **Media Management**: Spatie Media Library
- **Translations**: Spatie Translatable

## Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Composer

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Mahmoud-Eltwansy/E-commerce-APIs.git
cd ecommerce-apis
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment Variables

```bash
cp .env.example .env
```

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce-apis
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Create Database

```bash
mysql -u root -p
CREATE DATABASE ecommerce-apis;
EXIT;
```

### 6. Run Migrations

```bash
php artisan migrate
```
### 7. Edit Your APP_URL

Change the APP_URL in `.env` file to match your url\
If you are Laragon :`http://ecommerce-apis.test` \
If you are using local server (eg. Xampp) : `http://localhost:8000` And Run `php artisan migrate`

### 8. Seed Database

```bash
php artisan db:seed
```


## API Endpoints

### Authentication

#### Sign Up
- **Method**: POST
- **URL**: `/api/signup`
- **Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
}
```
- **Response**:
```json
{
    "message": "Signed Up successfully",
    "token": "token",
    "user": {
        "name": "John Doe",
        "email": "john@example.com",
        "updated_at": "2025-12-20T08:32:31.000000Z",
        "created_at": "2025-12-20T08:32:31.000000Z",
        "id": 2
    }
}
```

#### Sign In
- **Method**: POST
- **URL**: `/api/signin`
- **Body**:
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
- **Response**:
```json
{
    "message": "Signed in successfully",
    "token": "token",
    "user": {
        "name": "John Doe",
        "email": "john@example.com",
        "updated_at": "2025-12-20T08:32:31.000000Z",
        "created_at": "2025-12-20T08:32:31.000000Z",
        "id": 2
    }
}
```

#### Logout (Protected)
- **Method**: POST
- **URL**: `/api/logout`
- **Headers**: `Authorization: Bearer {token}`

### Products

#### Get All Products
- **Method**: GET
- **URL**: `/api/products?page=1&per_page=10`
- **Query Params**: page:1(default) , per_page:10(default) 
- **Response**:
```json
{
    "data": [
        {
            "id": 1,
            "title": {
                "en": "Computer",
                "ar": "كمبيوتر"
            },
            "description": {
                "en": "I'll stay down here! It'll be no doubt that it was just in time to hear it say, as it spoke. 'As.",
                "ar": "صحيح فلما فرغ من أكل التمرة رمى النواة وإذا هو بعفريت طويل القامة وبيده سيف فدنا من ذلك غاية العجب."
            },
            "price": "1502.13",
            "quantity": 190,
            "image_url": "http://ecommerce-apis.test/media/21/43.png"
        },
        {
            "id": 2,
            "title": {
                "en": "Laptop",
                "ar": "لابتوب"
            },
            "description": {
                "en": "Cat: 'we're all mad here. I'm mad. You're mad.' 'How do you know that Cheshire cats always.",
                "ar": "إليك فتفعل بي ما تريد والله على ما يحصل له وإذا بشيخ كبير قد أقبل عليه ومعه غزالة مسلسلة فسلم على."
            },
            "price": "2630.67",
            "quantity": 91,
            "image_url": "http://ecommerce-apis.test/media/20/42.png"
        }
    ],
    "current_page": 1,
    "last_page": 1,
    "next_page_url": null,
    "prev_page_url": null,
    "per_page": 10,
    "total": 2
}
```

#### Get Product by ID
- **Method**: GET
- **URL**: `/api/products/{id}`

### Cart (Protected Routes)

#### View Cart
- **Method**: GET
- **URL**: `/api/cart`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
    "message": "Cart retrieved successfully.",
    "cart": {
        "0": {
        
            "id": 12,
            "cart_quantity": 1,
            "product": {
                "id": 1,
                "title": {
                "en": "Computer",
                "ar": "كمبيوتر"
                 },
                "description": {
                "en": "I'll stay down here! It'll be no doubt that it was just in time to hear it say, as it spoke. 'As.",
                "ar": "صحيح فلما فرغ من أكل التمرة رمى النواة وإذا هو بعفريت طويل القامة وبيده سيف فدنا من ذلك غاية العجب."
                },
                "price": "1502.13",
                "quantity": 190,
                "image_url": "http://ecommerce-apis.test/media/21/43.png"
            }
        },
        "1": {
            "id": 13,
            "cart_quantity": 1,
            "product": {
                "id": 2,
                "title": {
                    "en": "Laptop",
                    "ar": "لابتوب"
                },
                "description": {
                    "en": "Cat: 'we're all mad here. I'm mad. You're mad.' 'How do you know that Cheshire cats always.",
                    "ar": "إليك فتفعل بي ما تريد والله على ما يحصل له وإذا بشيخ كبير قد أقبل عليه ومعه غزالة مسلسلة فسلم على."
                },
                "price": "2630.67",
                "quantity": 91,
                "image_url": "http://ecommerce-apis.test/media/20/42.png"
            }
        },
        "total_cart_price": 11190.28
    }
}
```

#### Add to Cart
- **Method**: POST
- **URL**: `/api/cart/add`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
```json
{
    "items":[
        {
            "product_id" : 1,
            "quantity" : 1

        },
        {
            "product_id":2,
            "quantity":1
        }
    ]
}
```

#### Remove from Cart
- **Method**: DELETE
- **URL**: `/api/cart/{product_id}`
- **Headers**: `Authorization: Bearer {token}`

### Orders (Protected Routes)

#### Create Order
- **Method**: POST
- **URL**: `/api/orders`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
    "message": "Order Created Successfully",
    "data": {
        "order_id": 1,
        "total_price": 11190.29,
        "status": "pending",
        "created_at": "2025-12-20T08:48:09.000000Z"
    }
}
```

#### Get User Orders
- **Method**: GET
- **URL**: `/api/orders`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
    "message": "Orders retrieved successfully",
    "data": [
        {
            "id": 4,
            "total_price": "18653.99",
            "status": "pending",
            "created_at": null
        },
        {
            "id": 5,
            "total_price": "11190.29",
            "status": "pending",
            "created_at": null
        }
    ]
}
```
#### Get Order by ID
- **Method**: GET
- **URL**: `/api/orders/{id}`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
```json
{
    "message": "Order retrieved successfully",
    "data": {
        "id": 5,
        "total_price": "11190.29",
        "status": "pending",
        "created_at": "2025-12-20T07:10:12.000000Z",
        "items": [
            {
                "product_id": 1,
                "title": {
                    "en": "Computer",
                    "ar": "كمبيوتر"
                },
                "description": {
                    "en": "Alice think it so VERY wide, but she had found the fan and a great letter, nearly as large as the.",
                    "ar": "يتخلصون إلا بعد عشر سنوات في هذا الحال فرأيت هذا الفتى قال الجني: إنها حكاية عجيبة وقد وهبت لك ثلث."
                },
                "price": "1492.74",
                "quantity": 1,
                "image": "http://ecommerce-apis.test/media/18/40.png"
            },
            {
                "product_id": 2,
                "title": {
                    "en": "Laptop",
                    "ar": "لابتوب"
                },
                "description": {
                    "en": "Alice, 'it would have done that?' she thought. 'I must go by the Hatter, and here the conversation.",
                    "ar": "بيني وبينك ثم إني عملت حساب الدكان من بربح مالي فوجدته ألفي دينار فحمدت الله عز وجل فأخذتها."
                },
                "price": "9697.55",
                "quantity": 1,
                "image": "http://ecommerce-apis.test/media/19/41.png"
            }
        ]
    }
}
```


## Database Schema

### Users
- id, name, email, password, created_at, updated_at

### Products
- id, title, description, price, quantity, created_at, updated_at

### Orders
- id, user_id, total_price, status, created_at, updated_at

### Order Items
- id, order_id, product_id, price, quantity, created_at, updated_at

### Carts
- id, user_id, product_id, quantity, created_at, updated_at

------


### This Project is Mady by Mahmoud Eltwansy
