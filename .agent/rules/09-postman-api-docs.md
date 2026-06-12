# Postman API Documentation Rule

Mọi API mới phát triển (thuộc `app/Api/V1`) PHẢI được đăng ký và cập nhật tài liệu trên Postman thông qua Postman MCP.

## 1. Quy trình thực hiện

1. **Sau khi viết API** (Controller, Request, Route):
   - Sử dụng `createCollectionRequest` để thêm API vào collection **"ĐẶT GÌ"** (UID: `53022322-7813f9d2-5cda-4030-a50f-9f76eb97c5e9`).
   - Nếu API đã tồn tại, sử dụng các công cụ update để đồng bộ thay đổi.

2. **Cấu trúc Folder**:
   - Phải nhóm các request vào folder theo **Module** (ví dụ: `Customer`, `Order`, `Product`).
   - Nếu module lớn, có thể chia nhỏ thành `Auth`, `Profile`, `Management`.

## 2. Cấu trúc Request tiêu chuẩn

- **Name**: `{Hành động} {Module}` bằng **Tiếng Việt** (ví dụ: `Danh sách Đơn hàng`, `Tạo Khách hàng`, `Cập nhật Tọa độ`).
- **Method**: Đúng HTTP method (GET, POST, PUT, DELETE).
- **URL**: Sử dụng `{{domain}}/{path}` (Ví dụ: `{{domain}}/location`).
- **Headers**:
  - `Accept: application/json`
  - `X-TOKEN-ACCESS: {{token}}`
- **Authentication**:
  - **Type**: `Bearer Token`
  - **Value (Token)** theo đối tượng:
    - **Customer**: `{{access_token}}`
    - **Employee**: `{{employee_access_token}}`
    - **Restaurant**: `{{restaurant_access_token}}`

## 3. Best Practices (BỔ SUNG)

### 3.1. Mô tả chi tiết (Description)
- Giải thích mục định của API.
- Liệt kê các quyền (Role) có thể truy cập.
- Chú thích các trường dữ liệu quan trọng hoặc logic đặc biệt.

### 3.2. Query Params & Body
- **Query Params**: Định nghĩa đầy đủ các filters (page, limit, status...) trong `queryParams`. Luôn có giá trị mặc định trong description.
- **Body**: Sử dụng `raw` (JSON). Key phải khớp 100% với `Request` validation của Laravel.

### 3.3. Scripts (Tự động hóa)
- **Tests**: Thêm mã kiểm tra cơ bản:
  ```javascript
  pm.test("Status code is 200", function () {
      pm.response.to.have.status(200);
  });
  ```
- **Pre-request**: Dùng để xử lý dữ liệu động nếu cần.

### 3.4. Example Responses (QUAN TRỌNG)
- Sau khi tạo request, phải tạo ít nhất 2 example:
  1. `Success {Status}` (ví dụ: `Success 200 OK`).
  2. `Error {Reason}` (ví dụ: `Validation Error 422`).
- Điều này giúp Frontend và Mobile có thể làm việc song song mà không cần chờ API thực sự hoàn tất.

## 4. Thông tin Context
- **Workspace**: `Đặt gì` (`a2456d21-90db-4b6e-ad96-ad27fc8a6508`)
- **Collection**: `ĐẶT GÌ` (`53022322-7813f9d2-5cda-4030-a50f-9f76eb97c5e9`)
