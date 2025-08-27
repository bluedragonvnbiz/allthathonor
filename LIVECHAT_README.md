# LiveChat Widget - All That Honors Club

## 📋 Overview

Real-time chat widget với SSE (Server-Sent Events) cho Honors Club theme. Hỗ trợ category selection flow và real-time messaging.

## 🚀 Features

- ✅ **Multi-stage Flow**: Welcome → Main Category → Sub Category → Chat
- ✅ **Real-time Messaging**: SSE stream cho instant messaging
- ✅ **Responsive Design**: Mobile-friendly widget
- ✅ **Operating Hours**: Tự động hiển thị giờ làm việc
- ✅ **Category Management**: Config-based categories
- ✅ **Admin Interface**: Existing admin panel integration
- ✅ **Security**: Nonce validation, input sanitization

## 📁 File Structure

```
app/
├── Views/partials/livechat/
│   ├── widget.php           # Widget HTML structure
│   └── init.php            # Initialization script
├── Services/
│   └── LiveChatService.php  # Business logic
├── Controllers/
│   └── LiveChatController.php # HTTP endpoints  
├── Ajax/
│   └── LiveChatAjaxHandler.php # AJAX handlers
├── Database/
│   └── LiveChatDatabase.php # Database schema
└── Helpers/
    └── LiveChatHelper.php   # Helper functions

assets/
├── css/
│   └── livechat-widget.css # Widget styles
└── js/
    └── livechat-widget.js  # Widget functionality

config/
└── livechat_categories.php # Categories configuration
```

## 🛠️ Installation

Widget đã được tự động initialize trong `functions.php`:

```php
// Initialize LiveChat Widget
LiveChatHelper::init();
```

## ⚙️ Configuration

### Categories Configuration

Edit `config/livechat_categories.php`:

```php
return [
    'main_categories' => [
        '직원 혜택' => [
            '서비스 이용',
            '멤버십 혜택', 
            '기타'
        ],
        '일반인 가기금' => [
            '상품 문의',
            '서비스 이용',
            '기타'
        ]
    ],
    
    'welcome_messages' => [
        'default' => '기본 환영 메시지',
        'staff_benefits' => '직원 혜택 전용 메시지'
    ]
];
```

### Operating Hours

Edit `LiveChatHelper.php` method `isOperatingHours()`:

```php
// Monday-Friday, 10 AM - 6 PM KST
$isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;
$isBusinessHour = $hour >= 10 && $hour < 18;
```

## 📡 API Endpoints

### AJAX Endpoints
- `livechat_start_session` - Tạo session mới
- `livechat_get_subcategories` - Lấy sub categories  
- `livechat_select_category` - Chọn category và start chat
- `livechat_send_message` - Gửi tin nhắn
- `livechat_close_session` - Đóng session

### Route Endpoints  
- `/chat/start` - Start session
- `/chat/stream` - SSE stream endpoint
- `/chat/send` - Send message
- `/chat/close` - Close session

## 🎨 Customization

### CSS Styling

Edit `assets/css/livechat-widget.css`:

```css
.chat-trigger {
    background: #89b97c; /* Change primary color */
}

.chat-window {
    width: 380px;        /* Change widget width */
    height: 600px;       /* Change widget height */
}
```

### JavaScript Events

```js
// Access widget instance
const widget = window.liveChatWidget;

// Custom event listeners
document.addEventListener('chatOpened', function() {
    console.log('Chat opened');
});

document.addEventListener('messageReceived', function(e) {
    console.log('New message:', e.detail);
});
```

## 🛡️ Security Features

- **Nonce Validation**: WordPress nonce cho AJAX requests
- **Input Sanitization**: Sanitize tất cả user inputs  
- **Session Validation**: Validate sessions trước khi processing
- **XSS Protection**: Escape HTML content
- **Rate Limiting**: Built-in WordPress AJAX throttling

## 📱 Mobile Support

Widget tự động responsive:
- **Desktop**: Fixed positioning, 380px width
- **Mobile**: Full width, overlay mode
- **Touch**: Optimized for touch interactions

## 🔧 Troubleshooting

### SSE Connection Issues

1. Check server EventSource support
2. Verify route `/chat/stream` accessibility
3. Check PHP timeout settings

### Widget Not Appearing

1. Verify `LiveChatHelper::init()` in functions.php
2. Check if `isEnabled()` returns true
3. Verify CSS/JS enqueuing

### Database Issues

1. Check if tables created: `wp_livechat_sessions`, `wp_livechat_messages`
2. Verify database permissions
3. Check error logs

## 📊 Database Schema

### Sessions Table (`wp_livechat_sessions`)
```sql
- id (bigint, primary key)
- session_id (varchar, unique)
- customer_name, customer_email, customer_phone
- category_main, category_sub  
- chat_stage (enum: category_main, category_sub, chat_active, closed)
- status (enum: waiting, active, closed)
- welcome_message_sent (boolean)
- created_at, updated_at, closed_at
```

### Messages Table (`wp_livechat_messages`)
```sql
- id (bigint, primary key)
- session_id (varchar, foreign key)
- sender_type (enum: customer, admin, system)
- message_type (enum: text, system, welcome, category_info)
- sender_name (varchar)
- message (text)
- metadata (json)
- is_read (boolean)
- created_at
```

## 🚦 Performance

### Optimization Features
- **Memory Limit**: 32MB per SSE connection
- **Connection Timeout**: 5 minutes idle timeout
- **Heartbeat**: 30 second intervals
- **Auto Reconnect**: Automatic SSE reconnection
- **Page Visibility**: Pause connections when page hidden

### Monitoring
- Check `error_log` for LiveChat errors
- Monitor server resource usage
- Track SSE connection counts

## 💡 Usage Examples

### Manual Initialization
```php
// In your template file
if (LiveChatHelper::isEnabled()) {
    include THEME_PATH . '/app/Views/partials/livechat/init.php';
}
```

### Custom Conditions
```php
// In LiveChatHelper::isEnabled()
// Show only on specific pages
if (is_page(['home', 'membership'])) {
    return true;
}

// Show only for logged-in users
if (is_user_logged_in()) {
    return true;
}

return false;
```

### Admin Integration
Widget tích hợp với existing admin panel tại `/admin/live-chat`.

---

## 🎯 Quick Start

1. Widget tự động load trên tất cả frontend pages
2. Click floating chat button ở góc dưới phải
3. Follow category selection flow
4. Start chatting!

**Widget đã sẵn sàng sử dụng!** 🚀