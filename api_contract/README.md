# API Contract Marketplace Application

Dokumen ini berisi spesifikasi lengkap kontrak API untuk seluruh modul dalam aplikasi Marketplace. Kontrak ini ditujukan untuk mempermudah integrasi antara Frontend (Web/Mobile) dengan Backend.

---

## DAFTAR ISI
1. [Standar Response & Error Handling](#1-standar-response--error-handling)
2. [Modul 1: Authentication & User Profile](#2-modul-1-authentication--user-profile)
3. [Modul 2: User Management (Admin Only)](#3-modul-2-user-management-admin-only)
4. [Modul 3: Store / Seller Management](#4-modul-3-store--seller-management)
5. [Modul 4: Buyer Profile Management](#5-modul-4-buyer-profile-management)
6. [Modul 5: Product Category](#6-modul-5-product-category)
7. [Modul 6: Product Management](#7-modul-6-product-management)
8. [Modul 7: Transaction / Checkout](#8-modul-7-transaction--checkout)
9. [Modul 8: Store Balance & History](#9-modul-8-store-balance--history)
10. [Modul 9: Withdrawal (Pencairan Saldo)](#10-modul-9-withdrawal-pencairan-saldo)
11. [Modul 10: Product Review (Ulasan Produk)](#11-modul-10-product-review-ulasan-produk)

---

## 1. Standar Response & Error Handling

Seluruh endpoint API mengembalikan data dalam format JSON dengan format standar sebagai berikut:

### Success Response (Umum)
```json
{
  "success": true,
  "message": "Pesan sukses dari server",
  "data": { ... } // Berupa object, array, atau null
}
```

### Error Response (HTTP 500, 404, 403, 401)
```json
{
  "success": false,
  "message": "Pesan error detail dari server",
  "data": null
}
```

### Validation Error Response (HTTP 422)
Terjadi jika data yang dikirim tidak lolos aturan validasi Laravel.
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "nama_field": [
      "Pesan error detail."
    ]
  }
}
```

---

## 2. Modul 1: Authentication & User Profile

### 2.1. Register User
Mendaftarkan akun baru.

*   **URL:** `/api/register`
*   **Method:** `POST`
*   **Headers:**
    *   `Accept: application/json`
    *   `Content-Type: application/json`
*   **Request Body (JSON):**
    ```json
    {
      "name": "Budi Seller",
      "email": "budi@seller.com",
      "password": "secretpassword",
      "role": "store" // Pilihan: "buyer" atau "store"
    }
    ```
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data Register",
      "data": {
        "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
        "name": "Budi Seller",
        "email": "budi@seller.com",
        "role": "store",
        "token": "1|sanctum_token_string_here"
      }
    }
    ```

### 2.2. Login User
Melakukan login untuk mendapatkan token API.

*   **URL:** `/api/login`
*   **Method:** `POST`
*   **Headers:**
    *   `Accept: application/json`
    *   `Content-Type: application/json`
*   **Request Body (JSON):**
    ```json
    {
      "email": "budi@seller.com",
      "password": "secretpassword"
    }
    ```
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Login Berhasil",
      "data": {
        "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
        "name": "Budi Seller",
        "email": "budi@seller.com",
        "role": "store",
        "token": "2|sanctum_token_string_here"
      }
    }
    ```
*   **Error Response (HTTP 401 Unauthorized):**
    ```json
    {
      "success": false,
      "message": "Email Atau Password Salah",
      "data": null
    }
    ```

### 2.3. Get My Profile (Me)
Mendapatkan info akun user terlogin berdasarkan token.

*   **URL:** `/api/me`
*   **Method:** `GET`
*   **Auth Required:** Yes (Bearer Token)
*   **Headers:**
    *   `Authorization: Bearer <token>`
    *   `Accept: application/json`
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "My Profile",
      "data": {
        "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
        "profile_picture": "http://domain.com/storage/default.png",
        "name": "Budi Seller",
        "email": "budi@seller.com",
        "role": "store",
        "permissions": [
          "dashboard-menu",
          "store-menu",
          "product-list",
          "product-create"
        ],
        "store": {
          "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
          "name": "Toko Budi Jaya",
          "username": "toko-budi-jaya"
        }
      }
    }
    ```

### 2.4. Logout
Menghapus token login aktif.

*   **URL:** `/api/logout`
*   **Method:** `POST`
*   **Auth Required:** Yes (Bearer Token)
*   **Headers:**
    *   `Authorization: Bearer <token>`
    *   `Accept: application/json`
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Logout Berhasil",
      "data": null
    }
    ```

---

## 3. Modul 2: User Management (Admin Only)

### 3.1. List Users
Mendapatkan semua daftar user.

*   **URL:** `/api/user`
*   **Method:** `GET`
*   **Auth Required:** Yes (Admin Only)
*   **Headers:**
    *   `Authorization: Bearer <token>`
    *   `Accept: application/json`
*   **Query Parameters:**
    *   `search` (string, optional) - Pencarian berdasarkan nama/email.
    *   `limit` (integer, optional) - Batasan baris.
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": [
        {
          "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
          "profile_picture": "http://domain.com/storage/assets/profiles/pic.png",
          "name": "Budi Seller",
          "email": "budi@seller.com",
          "role": "store"
        }
      ]
    }
    ```

### 3.2. List Users Paginated
*   **URL:** `/api/user/all/paginated`
*   **Method:** `GET`
*   **Auth Required:** Yes (Admin Only)
*   **Query Parameters:**
    *   `search` (string, optional) - Pencarian nama/email.
    *   `row_per_page` (integer, **required**) - Jumlah baris per halaman.
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": {
        "data": [
          {
            "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
            "profile_picture": "http://domain.com/storage/default.png",
            "name": "Budi Seller",
            "email": "budi@seller.com",
            "role": "store"
          }
        ],
        "meta": {
          "current_page": 1,
          "from": 1,
          "last_page": 5,
          "per_page": 10,
          "to": 10,
          "total": 50
        },
        "links": {
          "first": "http://domain.com/api/user/all/paginated?page=1",
          "last": "http://domain.com/api/user/all/paginated?page=5",
          "prev": null,
          "next": "http://domain.com/api/user/all/paginated?page=2"
        }
      }
    }
    ```

### 3.3. Detail User
*   **URL:** `/api/user/{id}`
*   **Method:** `GET`
*   **Auth Required:** Yes (Admin Only)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": {
        "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
        "profile_picture": "http://domain.com/storage/default.png",
        "name": "Budi Seller",
        "email": "budi@seller.com",
        "role": "store"
      }
    }
    ```

### 3.4. Create User
*   **URL:** `/api/user`
*   **Method:** `POST`
*   **Auth Required:** Yes (Admin Only)
*   **Request Body (JSON):**
    ```json
    {
      "name": "Budi Baru",
      "email": "budibaru@domain.com",
      "password": "secretpassword"
    }
    ```
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": {
        "id": "b110899f-7bd4-4a41-bf7b-bd76b91129aa",
        "profile_picture": "http://domain.com/storage/default.png",
        "name": "Budi Baru",
        "email": "budibaru@domain.com",
        "role": "-"
      }
    }
    ```

### 3.5. Update User
*   **URL:** `/api/user/{id}`
*   **Method:** `PUT` / `PATCH`
*   **Auth Required:** Yes (Admin Only)
*   **Request Body (JSON):**
    ```json
    {
      "name": "Budi Edit",
      "password": "newsecretpassword" // Optional
    }
    ```
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": {
        "id": "b110899f-7bd4-4a41-bf7b-bd76b91129aa",
        "profile_picture": "http://domain.com/storage/default.png",
        "name": "Budi Edit",
        "email": "budibaru@domain.com",
        "role": "-"
      }
    }
    ```

### 3.6. Delete User
*   **URL:** `/api/user/{id}`
*   **Method:** `DELETE`
*   **Auth Required:** Yes (Admin Only)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User Deleted",
      "data": null
    }
    ```

---

## 4. Modul 3: Store / Seller Management

### 4.1. List Stores (Public)
Mendapatkan daftar semua toko terdaftar.

*   **URL:** `/api/store`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional) - Pencarian nama toko atau telepon.
    *   `is_verified` (boolean, optional) - Filter toko terverifikasi (`true`/`false`).
    *   `limit` (integer, optional)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": [
        {
          "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
          "name": "Toko Budi Jaya",
          "username": "toko-budi-jaya",
          "logo": "http://domain.com/storage/assets/store/logo.png",
          "about": "Penyedia barang olahraga",
          "phone": "081234567890",
          "address_id": "1",
          "address": "Jl. Mawar No. 10",
          "city": "Surabaya",
          "postal_code": "60111",
          "is_verified": true,
          "product_count": 5,
          "transaction_count": 1
        }
      ]
    }
    ```

### 4.2. List Stores Paginated (Public)
*   **URL:** `/api/store/all/paginated`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `is_verified` (boolean, optional)
    *   `row_per_page` (integer, **required**)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": {
        "data": [
          {
            "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
            "name": "Toko Budi Jaya",
            "username": "toko-budi-jaya",
            "logo": "http://domain.com/storage/assets/store/logo.png",
            "about": "Penyedia barang olahraga",
            "phone": "08123456789",
            "address_id": "1",
            "address": "Jl. Mawar No. 10",
            "city": "Surabaya",
            "postal_code": "60111",
            "is_verified": true,
            "product_count": 5,
            "transaction_count": 1
          }
        ],
        "meta": { "current_page": 1, "per_page": 10, "total": 1 }
      }
    }
    ```

### 4.3. Detail Store by ID (Public)
*   **URL:** `/api/store/{id}`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    *Format data sama dengan detail Store.*

### 4.4. Detail Store by Username (Public)
*   **URL:** `/api/store/username/{username}`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    *Format data sama dengan detail Store.*

### 4.5. Get My Store (Khusus Seller)
Mengambil informasi detail toko milik user login.

*   **URL:** `/api/my-store`
*   **Method:** `GET`
*   **Auth Required:** Yes (Bearer Token)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Store",
      "data": {
        "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
        "name": "Toko Budi Jaya",
        "username": "toko-budi-jaya",
        "logo": "http://domain.com/storage/assets/store/logo.png",
        "about": "Penyedia barang olahraga",
        "phone": "081234567890",
        "address_id": "1",
        "address": "Jl. Mawar No. 10",
        "city": "Surabaya",
        "postal_code": "60111",
        "is_verified": true,
        "product_count": 5,
        "transaction_count": 1
      }
    }
    ```

### 4.6. Create Store (Registrasi Toko)
*   **URL:** `/api/store`
*   **Method:** `POST`
*   **Auth Required:** Yes
*   **Headers:**
    *   `Content-Type: multipart/form-data`
*   **Request Body (Form Data):**
    *   `name` (string, **required**) - Nama toko.
    *   `user_id` (uuid, **required**) - ID User pemilik.
    *   `logo` (file image, **required**) - Format: png, jpg, jpeg (maks 2MB).
    *   `about` (string, **required**)
    *   `phone` (string, **required**)
    *   `address_id` (string, **required**)
    *   `address` (string, **required**)
    *   `city` (string, **required**)
    *   `postal_code` (string, **required**)
    *   `is_verified` (boolean, **required**) - default false.
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": {
        "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
        "name": "Toko Budi Jaya",
        "username": "toko-budi-jaya",
        "logo": "http://domain.com/storage/assets/store/logo.png",
        "about": "Penyedia barang olahraga",
        "phone": "081234567890",
        "address_id": "1",
        "address": "Jl. Mawar No. 10",
        "city": "Surabaya",
        "postal_code": "60111",
        "is_verified": false
      }
    }
    ```

### 4.7. Update Store
Mengubah data profil toko.

*   **URL:** `/api/store/{id}`
*   **Method:** `POST` (Dengan field `_method` = `PUT` / `PATCH` untuk form-data)
*   **Auth Required:** Yes
*   **Request Body (Form Data):**
    *   `_method` (string) - `PUT`
    *   `name` (string, **required**)
    *   `logo` (file image, optional) - Format: png, jpg, jpeg (maks 2MB).
    *   `about` (string, **required**)
    *   `phone` (string, **required**)
    *   `address_id` (string, **required**)
    *   `address` (string, **required**)
    *   `city` (string, **required**)
    *   `postal_code` (string, **required**)
*   **Success Response (HTTP 200 OK):**
    *Format sama dengan detail Store.*

### 4.8. Verifikasi Toko (Admin Only)
Menandai toko telah terverifikasi.

*   **URL:** `/api/store/{id}/verified`
*   **Method:** `PUT`
*   **Auth Required:** Yes (Role `admin`)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Store Berhasil Diverifikasi",
      "data": {
        "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
        "name": "Toko Budi Jaya",
        "is_verified": true
      }
    }
    ```

---

## 5. Modul 4: Buyer Profile Management

### 5.1. List Buyers (Admin Only)
*   **URL:** `/api/buyer`
*   **Method:** `GET`
*   **Auth Required:** Yes (Admin Only)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Buyer",
      "data": [
        {
          "id": "e220899f-7bd4-4a41-bf7b-bd76b91129ee",
          "profile_picture": "http://domain.com/storage/assets/profiles/buyer.png",
          "phone_number": "08987654321",
          "user": {
            "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
            "name": "Budi Buyer",
            "email": "buyer@domain.com"
          }
        }
      ]
    }
    ```

### 5.2. List Buyers Paginated (Admin Only)
*   **URL:** `/api/buyer/all/paginated`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `row_per_page` (integer, **required**)
*   **Success Response (HTTP 200 OK):**
    *Format pagination dengan data buyer.*

### 5.3. Create Buyer Profile
*   **URL:** `/api/buyer`
*   **Method:** `POST`
*   **Auth Required:** Yes
*   **Request Body (Form Data):**
    *   `user_id` (uuid, **required**) - ID User.
    *   `profile_picture` (file image, **required**) - format png, jpg, jpeg.
    *   `phone_number` (string, **required**)
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data Buyer Created",
      "data": {
        "id": "e220899f-7bd4-4a41-bf7b-bd76b91129ee",
        "profile_picture": "http://domain.com/storage/assets/profiles/buyer.png",
        "phone_number": "08987654321"
      }
    }
    ```

### 5.4. Update Buyer Profile
*   **URL:** `/api/buyer/{id}`
*   **Method:** `POST` (Dengan `_method` = `PUT`)
*   **Auth Required:** Yes
*   **Request Body (Form Data):**
    *   `_method` (string) - `PUT`
    *   `user_id` (uuid, **required**)
    *   `profile_picture` (file image, optional)
    *   `phone_number` (string, **required**)
*   **Success Response (HTTP 200 OK):**
    *Format sama dengan detail Buyer.*

---

## 6. Modul 5: Product Category

### 6.1. Public List Categories
*   **URL:** `/api/product-category`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Product Category",
      "data": [
        {
          "id": "a820899f-7bd4-4a41-bf7b-bd76b91129aa",
          "name": "Sepatu Olahraga",
          "slug": "sepatu-olahraga",
          "image": "http://domain.com/storage/assets/categories/shoes.png",
          "tagline": "Sepatu olahraga berkualitas",
          "description": "Kategori semua jenis sepatu olahraga",
          "product_count": 12,
          "children_count": 0
        }
      ]
    }
    ```

### 6.2. Public List Categories Paginated
*   **URL:** `/api/product-category/all/paginated`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `row_per_page` (integer, **required**)
*   **Success Response (HTTP 200 OK):**
    *Format paginated dengan data kategori.*

### 6.3. Public Category Detail by Slug
*   **URL:** `/api/product-category/slug/{slug}`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    *Format detail kategori.*

### 6.4. Create Category (Admin Only)
*   **URL:** `/api/product-category`
*   **Method:** `POST`
*   **Auth Required:** Yes (Role `admin`)
*   **Request Body (Form Data):**
    *   `name` (string, **required**, unique)
    *   `tagline` (string, **required**)
    *   `description` (string, **required**)
    *   `parent_id` (uuid, optional) - ID Kategori induk jika ada.
    *   `image` (file image, **required**) - png, jpg, jpeg (maks 2MB).
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data Product Category Created",
      "data": {
        "id": "a820899f-7bd4-4a41-bf7b-bd76b91129aa",
        "name": "Sepatu Olahraga",
        "slug": "sepatu-olahraga",
        "image": "http://domain.com/storage/assets/categories/shoes.png",
        "tagline": "Sepatu olahraga berkualitas",
        "description": "Kategori semua jenis sepatu olahraga"
      }
    }
    ```

### 6.5. Update Category (Admin Only)
*   **URL:** `/api/product-category/{id}`
*   **Method:** `POST` (Dengan `_method` = `PUT`)
*   **Auth Required:** Yes (Role `admin`)
*   **Request Body (Form Data):**
    *   `_method` (string) - `PUT`
    *   `name` (string, **required**)
    *   `tagline` (string, **required**)
    *   `description` (string, **required**)
    *   `parent_id` (uuid, optional)
    *   `image` (file image, optional)
*   **Success Response (HTTP 200 OK):**
    *Format sama dengan detail kategori.*

### 6.6. Delete Category (Admin Only)
*   **URL:** `/api/product-category/{id}`
*   **Method:** `DELETE`
*   **Auth Required:** Yes (Role `admin`)
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Product Category Deleted",
      "data": null
    }
    ```

---

## 7. Modul 6: Product Management

### 7.1. List Products (Public)
*   **URL:** `/api/product`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional) - Pencarian nama produk.
    *   `store_id` (uuid, optional) - Filter produk toko tertentu.
    *   `product_category_id` (uuid, optional) - Filter produk kategori tertentu.
    *   `is_random` (boolean, optional) - Tampilkan acak (`true`/`false`).
    *   `limit` (integer, optional) - Limit data.
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Product",
      "data": [
        {
          "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
          "name": "Sepatu Lari Nike Air",
          "slug": "sepatu-lari-nike-air-1",
          "condition": "new",
          "price": 1200000,
          "stock": 10,
          "weight": "0.50",
          "description": "Sepatu lari super nyaman.",
          "product_category": {
            "id": "a820899f-7bd4-4a41-bf7b-bd76b91129aa",
            "name": "Sepatu Olahraga",
            "slug": "sepatu-olahraga"
          },
          "product_images": [
            {
              "id": "e720899f-7bd4-4a41-bf7b-bd76b91129bb",
              "image": "http://domain.com/storage/assets/products/shoes1.png",
              "is_thumbnail": true
            }
          ]
        }
      ]
    }
    ```

### 7.2. List Products Paginated (Public)
*   **URL:** `/api/product/all/paginated`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search`, `store_id`, `product_category_id`, `is_random`
    *   `row_per_page` (integer, **required**)
*   **Success Response (HTTP 200 OK):**
    *Format data paginated.*

### 7.3. List Products Seller (Khusus Seller)
Mengambil daftar produk milik seller terlogin.
*   **URL:** `/api/seller/product` (atau `GET /api/product?seller_view=true`)
*   **Method:** `GET`
*   **Auth Required:** Yes (Bearer Token)

### 7.4. Detail Product (by ID or Slug)
*   **URL:** `/api/product/{id}` ATAU `/api/product/slug/{slug}`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    *Format detail produk beserta relasi `store` dan `product_reviews`.*

### 7.5. Create Product (Khusus Seller)
*   **URL:** `/api/product`
*   **Method:** `POST`
*   **Auth Required:** Yes
*   **Request Body (Form Data):**
    *   `store_id` (uuid, **required**) - ID Toko.
    *   `product_category_id` (uuid, **required**) - ID Kategori (harus kategori anak).
    *   `name` (string, **required**)
    *   `about` (string, **required**) - Deskripsi.
    *   `price` (integer, **required**)
    *   `stock` (integer, **required**)
    *   `weight` (decimal, **required**) - Berat.
    *   `condition` (string, **required**) - `new` atau `seccond`.
    *   `product_images` (array, **required**, min: 1)
    *   `product_images[0][image]` (file image, **required**) - png, jpg, jpeg (maks 2MB).
    *   `product_images[0][is_thumbnail]` (boolean, **required**) - tepat satu harus `true`.
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data Product",
      "data": {
        "id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
        "name": "Sepatu Lari Nike Air",
        "slug": "sepatu-lari-nike-air-1",
        "condition": "new",
        "price": 1200000,
        "stock": 10,
        "weight": "0.50",
        "description": "Sepatu lari super nyaman.",
        "product_images": [
          {
            "id": "e720899f-7bd4-4a41-bf7b-bd76b91129bb",
            "image": "http://domain.com/storage/assets/products/shoes1.png",
            "is_thumbnail": true
          }
        ]
      }
    }
    ```

### 7.6. Update Product (Khusus Seller)
*   **URL:** `/api/product/{id}`
*   **Method:** `POST` (Dengan `_method` = `PUT`)
*   **Request Body (Form Data):**
    *   `_method` (string) - `PUT`
    *   `store_id`, `product_category_id`, `name`, `about`, `price`, `stock`, `weight`, `condition`
    *   `deleted_product_images` (array, optional) - Array dari ID gambar (`product_images.id`) yang ingin dihapus.
    *   `product_images` (array, optional) - Gambar baru yang ingin ditambahkan.

### 7.7. Delete Product (Khusus Seller)
*   **URL:** `/api/product/{id}`
*   **Method:** `DELETE`
*   **Success Response (HTTP 200 OK):**
    `"message": "Data Product Deleted"`

---

## 8. Modul 7: Transaction / Checkout

### 8.1. Create Transaction (Checkout)
*   **URL:** `/api/transaction`
*   **Method:** `POST`
*   **Auth Required:** Yes
*   **Request Body (JSON):**
    ```json
    {
      "store_id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
      "buyer_id": "e220899f-7bd4-4a41-bf7b-bd76b91129ee",
      "address_id": 1,
      "address": "Jl. Merdeka No. 12",
      "city": "Surabaya",
      "postal_code": "60111",
      "shipping": "JNE",
      "shipping_type": "REG",
      "products": [
        {
          "product_id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
          "qty": 2
        }
      ]
    }
    ```
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Transaksi",
      "data": {
        "id": "e210899f-7bd4-4a41-bf7b-bd76b9112911",
        "buyer": { "id": "e220899f-7bd4-4a41-bf7b-bd76b91129ee" },
        "store": { "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc" },
        "address": "Jl. Merdeka No. 12",
        "city": "Surabaya",
        "postal_code": "60111",
        "shipping": "JNE",
        "shipping_type": "REG",
        "shipping_cost": 20000,
        "tracking_number": null,
        "tax": 2500,
        "grand_total": 2422500,
        "payment_status": "pending",
        "delivery_status": "pending",
        "snap_token": "midtrans_snap_token_here",
        "transaction_detail": [
          {
            "id": "f820899f-7bd4-4a41-bf7b-bd76b91129ff",
            "product_id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
            "qty": 2,
            "price": 1200000
          }
        ]
      }
    }
    ```

### 8.2. List Transactions (User Terlogin)
Melihat riwayat pembelian (untuk buyer) atau pesanan masuk (untuk seller).

*   **URL:** `/api/transaction`
*   **Method:** `GET`
*   **Auth Required:** Yes
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `limit` (integer, optional)
*   **Success Response (HTTP 200 OK):**
    *Mengembalikan array data transaksi.*

### 8.3. List Transactions Paginated (User Terlogin)
*   **URL:** `/api/transaction/all/paginated`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `row_per_page` (integer, **required**)

### 8.4. Detail Transaction
*   **URL:** `/api/transaction/{id}`
*   **Method:** `GET`

### 8.5. Get Transaction by Code (Pelacakan)
*   **URL:** `/api/transaction/code/{code}`
*   **Method:** `GET`

### 8.6. Update Transaction Status (Seller / Kurir)
Mengubah status pengiriman atau memasukkan nomor resi.

*   **URL:** `/api/transaction/{id}`
*   **Method:** `POST` (Dengan `_method` = `PUT` / `PATCH` di form-data)
*   **Request Body (Form Data):**
    *   `_method` (string) - `PUT`
    *   `status` (string, **required**) - Pilihan: `processing`, `delivering`, `canceled`, `completed`.
    *   `tracking_number` (string, optional) - Nomor resi pengiriman.
    *   `delivery_proof` (file image, optional) - Bukti pengiriman (png, jpg, jpeg, maks 5MB).
*   **Success Response (HTTP 200 OK):**
    *Status pengiriman berhasil diubah.*

### 8.7. Delete Transaction
*   **URL:** `/api/transaction/{id}`
*   **Method:** `DELETE`

---

## 9. Modul 8: Store Balance & History

### 9.1. List Store Balances
*   **URL:** `/api/store-balance`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Buyer",
      "data": [
        {
          "id": "c110899f-7bd4-4a41-bf7b-bd76b91129bb",
          "balance": 2500000,
          "store": {
            "id": "d820899f-7bd4-4a41-bf7b-bd76b91129cc",
            "name": "Toko Budi Jaya"
          }
        }
      ]
    }
    ```

### 9.2. List Store Balances Paginated
*   **URL:** `/api/store-balance/all/paginated`
*   **Method:** `GET`

### 9.3. Detail Store Balance
*   **URL:** `/api/store-balance/{id}`
*   **Method:** `GET`

### 9.4. List Store Balance Histories (Mutasi Saldo)
*   **URL:** `/api/store-balance-history`
*   **Method:** `GET`
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data User",
      "data": [
        {
          "amount": 1200000,
          "type": "credit", // "credit" (masuk) atau "debit" (keluar)
          "reference_id": "e210899f-7bd4-4a41-bf7b-bd76b9112911",
          "reference_type": "App\\Models\\Transaction",
          "remarks": "Penjualan Produk Sepatu Nike",
          "store_balance": {
            "id": "c110899f-7bd4-4a41-bf7b-bd76b91129bb",
            "balance": 2500000
          }
        }
      ]
    }
    ```

### 9.5. List Store Balance Histories Paginated
*   **URL:** `/api/store-balance-history/all/paginated`
*   **Method:** `GET`

---

## 10. Modul 9: Withdrawal (Pencairan Saldo)

### 10.1. Request Withdrawal (Tarik Saldo)
*   **URL:** `/api/withdrawal`
*   **Method:** `POST`
*   **Auth Required:** Yes
*   **Request Body (JSON):**
    ```json
    {
      "store_balance_id": "c110899f-7bd4-4a41-bf7b-bd76b91129bb",
      "amount": 500000,
      "bank_name": "bca", // Pilihan: bca, bni, bri, mandiri
      "bank_account_name": "Budi Santoso",
      "bank_account_number": "1234567890"
    }
    ```
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data Withdrawal",
      "data": {
        "id": "w110899f-7bd4-4a41-bf7b-bd76b91129dd",
        "amount": 500000,
        "bank_name": "bca",
        "bank_account_name": "Budi Santoso",
        "bank_account_number": "1234567890",
        "status": "pending",
        "created_at": "2026-07-17T20:30:00Z"
      }
    }
    ```

### 10.2. List Withdrawals
*   **URL:** `/api/withdrawal`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `limit` (integer, optional)

### 10.3. List Withdrawals Paginated
*   **URL:** `/api/withdrawal/all/paginated`
*   **Method:** `GET`
*   **Query Parameters:**
    *   `search` (string, optional)
    *   `row_per_page` (integer, **required**)

### 10.4. Detail Withdrawal
*   **URL:** `/api/withdrawal/{id}`
*   **Method:** `GET`

### 10.5. Approve Withdrawal (Admin Only)
Admin mengunggah bukti transfer untuk merubah status menjadi success.

*   **URL:** `/api/withdrawal/{id}/approve`
*   **Method:** `POST` (Dengan `_method` = `PUT` di form-data)
*   **Auth Required:** Yes (Admin Only)
*   **Request Body (Form Data):**
    *   `_method` (string) - `PUT`
    *   `proof` (file image, **required**) - Bukti transfer (png, jpg, jpeg, maks 2MB).
*   **Success Response (HTTP 200 OK):**
    ```json
    {
      "success": true,
      "message": "Data Withdrawal Disetujui",
      "data": {
        "id": "w110899f-7bd4-4a41-bf7b-bd76b91129dd",
        "status": "success",
        "proof": "http://domain.com/storage/assets/withdrawals/proof.png"
      }
    }
    ```

---

## 11. Modul 10: Product Review (Ulasan Produk)

### 11.1. Create Product Review
Pembeli memberikan rating dan review untuk produk dari transaksi yang berhasil.

*   **URL:** `/api/product-review`
*   **Method:** `POST`
*   **Auth Required:** Yes (Role `buyer`)
*   **Request Body (JSON):**
    ```json
    {
      "transaction_id": "e210899f-7bd4-4a41-bf7b-bd76b9112911",
      "product_id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
      "rating": 5, // Tipe: numeric/float dari 1 hingga 5
      "review": "Bahan sepatu sangat empuk dan nyaman dipakai!"
    }
    ```
*   **Success Response (HTTP 201 Created):**
    ```json
    {
      "success": true,
      "message": "Data Product Review",
      "data": {
        "id": "r110899f-7bd4-4a41-bf7b-bd76b91129ee",
        "transaction_id": "e210899f-7bd4-4a41-bf7b-bd76b9112911",
        "product_id": "9d90899f-7bd4-4a41-bf7b-bd76b91129f9",
        "rating": 5,
        "review": "Bahan sepatu sangat empuk dan nyaman dipakai!"
      }
    }
    ```
