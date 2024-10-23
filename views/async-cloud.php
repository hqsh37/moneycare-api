<?php
// Chuỗi JSON mẫu
$jsonString = '[
  {
    "type": "create",
    "tbl": "category",
    "id": "category_12345",
    "data": {
      "name": "Ăn uống",
      "icon": "hamburger",
      "iconLib": "FontAwesome5",
      "type": "chi",
      "desc": "Hello world"
    }
  },
  {
    "type": "update",
    "tbl": "category",
    "id": "category_12345",
    "data": {
      "name": "Ăn uống và giải khát",
      "icon": "hamburger",
      "iconLib": "FontAwesome5",
      "type": "chi"
    }
  },
  {
    "type": "create",
    "tbl": "account",
    "id": "account_67890",
    "data": {
      "name": "Tài khoản ngân hàng",
      "balance": 5000000,
      "type": "bank"
    }
  },
  {
    "type": "update",
    "tbl": "account",
    "id": "account_67890",
    "data": {
      "name": "Tài khoản ngân hàng",
      "balance": 10000000,
      "type": "bank"
    }
  },
  {
    "type": "create",
    "tbl": "transaction",
    "id": "transaction_77572",
    "data": {
      "account_id": "account_67890",
      "category_id": "category_12345",
      "amount": 50000,
      "date": "2022-01-01",
      "type": "chi",
      "desc": "Mua hamburger"
    }
  }
]';

// Sử dụng hàm để xử lý chuỗi JSON
$result = DataProcessor::processData($jsonString, 2);

// Hiển thị kết quả
var_dump($result);


// $key = 'job'; 
// $variable = null;

// if (assignIfKeyExists($key, $variable, $assocArray)) {
//   echo "Giá trị của key '$key' là: $variable\n";
// } else {
//   echo "Key '$key' không tồn tại trong mảng.\n";
// }