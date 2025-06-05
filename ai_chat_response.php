<?php
// ai_chat_response.php
header("Content-Type: application/json");

// User message from AJAX POST request
$userMessage = strtolower(trim($_POST['message'] ?? ''));

// Response patterns with Hindi & English
$responsePatterns = [
    [
        "keywords" => ["order", "track order", "mera order track karo", "my order", "order kaha", "order kidhar", "mera order"],
        "english" => "You can track your order in the 'My Orders' section.",
        "hindi" => "Aap 'My Orders' section mein jaa kar apne order ko track kar sakte hain."
    ],
    [
        "keywords" => ["cancel", "order cancel", "cancel karo", "cancel my order", "order cancel please"],
        "english" => "To cancel your order, please visit the 'My Orders' section and click cancel.",
        "hindi" => "Order cancel karne ke liye 'My Orders' section mein jaakar cancel par click karein."
    ],
    [
        "keywords" => ["return", "return kaise", "return product", "wapis kaise", "product wapas", "return karna hai"],
        "english" => "To return a product, go to 'My Orders' and tap on 'Return'.",
        "hindi" => "Product return karne ke liye 'My Orders' mein jaakar 'Return' par click karein."
    ],
    [
        "keywords" => ["refund", "refund kab", "refund kab tak aaega", "refund kitna", "paise wapas", "return money", "refund milega"],
        "english" => "Refunds are processed within 5-7 business days after pickup.",
        "hindi" => "Pickup ke baad 5-7 business dinon mein refund process ho jata hai."
    ],
    [
        "keywords" => ["coupon", "discount", "promo code", "offer code", "coupon use"],
        "english" => "Apply your coupon on the checkout page to get a discount.",
        "hindi" => "Discount ke liye checkout page par coupon apply karein."
    ],
    [
        "keywords" => ["payment", "pay", "payment method", "upi", "cod", "cash on delivery"],
        "english" => "We support UPI, Wallets, and Cash on Delivery (COD).",
        "hindi" => "Hum UPI, Wallets aur Cash on Delivery (COD) support karte hain."
    ],
    [
        "keywords" => ["support", "help", "chat", "customer care", "contact", "call", "support ticket"],
        "english" => "We’re here to help! Raise a ticket from the Support section or chat with us.",
        "hindi" => "Hum madad ke liye yahan hain! Support section mein ticket raise karein ya humse chat karein."
    ],
    [
        "keywords" => ["address", "change address", "mera address change karo", "address kaise change karu", "location kaise change karu", "address update", "location change"],
        "english" => "Go to your Profile section and update your address from there.",
        "hindi" => "Profile section mein jaa kar aap apna address update kar sakte hain."
    ],
    [
        "keywords" => ["thanks", "thnx", "thank you", "ok","okey","thik hai","byy","by", "shukriya", "madad ke liye", "dhanyawad"],
        "english" => "You're welcome! Let me know if I can help you with anything else. 😊",
        "hindi" => "Aapka swagat hai! Agar kisi aur cheez mein madad chahiye toh zaroor batayein. 😊"
    ],
    [
        "keywords" => ["hello", "hi", "namaste", "salam", "hey"],
        "english" => "Hello! How can I assist you today?",
        "hindi" => "Namaste! Main aapki kis tarah madad kar sakta hoon?"
    ],
    [
        "keywords" => ["wishlist", "wishlist kaise", "add to wishlist", "favourite", "pasandida"],
        "english" => "You can manage your Wishlist from the product page or your profile.",
        "hindi" => "Aap apni Wishlist ko product page ya profile se manage kar sakte hain."
    ],
    [
        "keywords" => ["delivery", "kab milega", "delivery kab", "delivery date", "delivery status", "kab tak aayega"],
        "english" => "Delivery details are available in 'My Orders'. You'll also receive SMS/Email updates.",
        "hindi" => "Delivery ki jankari 'My Orders' mein milegi. Aapko SMS/Email bhi milega."
    ],
    [
        "keywords" => ["available", "product available", "stock", "item in stock", "item out of stock"],
        "english" => "You can check product availability directly on the product page.",
        "hindi" => "Product ki availability aapko product page par dikhegi."
    ],
    [
        "keywords" => ["app", "mobile app", "snapbazaar app", "download app", "android app"],
        "english" => "Our mobile app is coming soon! Stay tuned for updates.",
        "hindi" => "Hamari mobile app jald aa rahi hai! Updates ke liye bane rahiye."
    ],
    [
        "keywords" => ["offers", "latest offers", "sale", "flash sale", "deals"],
        "english" => "Check out the homepage and 'Deals' section for all latest offers and discounts!",
        "hindi" => "Homepage aur 'Deals' section mein sabhi latest offers aur discounts dekhein!"
    ],
    [
        
        "keywords" => ["profile", "edit profile", "update name", "change email", "profile change"],
        "english" => "Go to the Profile section to update your name, mobile number, and email.",
        "hindi" => "Profile section mein jaa kar apna naam, mobile number aur email update karein."
    ],
    [
        "keywords" => ["login", "signup", "register", "account banaye", "register kaise karu"],
        "english" => "You can sign up or log in from the top right corner of the page or from the app.",
        "hindi" => "Aap page ke top right corner se ya app ke madhyam se sign up/login kar sakte hain."
    ],
    [
        "keywords" => ["logout", "signout", "log out" , "logout kaise kare" ],
        "english" => "To logout, click on your profile icon and select 'Logout'.",
        "hindi" => "Logout karne ke liye profile icon par click karke 'Logout' chunein."
    ]
    
];

// Detect Hindi or English based on Unicode pattern
function detectLanguage($text) {
    if (preg_match('/[\x{0900}-\x{097F}]/u', $text)) {
        return "hindi";
    }
    return "english";
}

// Smart match finder using keyword list
function getSmartResponse($input, $patterns) {
    $lang = detectLanguage($input);
    foreach ($patterns as $pattern) {
        foreach ($pattern['keywords'] as $keyword) {
            if (strpos($input, $keyword) !== false) {
                return $pattern[$lang] ?? $pattern["english"];
            }
        }
    }

    // No match found, return fallback
    if ($lang === "hindi") {
        return "Maaf kijiye, main aapki baat samajh nahi paaya. Kripya thoda aur spasht likhiye.";
    } else {
        return "Sorry, I couldn't understand that. Can you rephrase your question?";
    }
}

// Final smart response
$response = getSmartResponse($userMessage, $responsePatterns);
echo json_encode(["response" => $response]);
?>
