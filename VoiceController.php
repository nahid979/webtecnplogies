<?php
// controllers/VoiceController.php
require_once 'models/ListModel.php';


if (!function_exists('mb_strlen')) {
    function mb_strlen($str, $encoding = 'UTF-8') {
        return count(preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY));
    }
}

class VoiceController {
    private $listModel;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $this->listModel = new ListModel();
    }
    
    public function voiceAdd() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $voice_text = $_POST['voice_text'] ?? '';
            
            if (empty($voice_text)) {
                echo json_encode(['success' => false, 'message' => 'কিছুই বলেননি!']);
                return;
            }
            
            $items = $this->extractItemsWithQuantity($voice_text);
            
            if (count($items) == 0) {
                echo json_encode(['success' => false, 'message' => 'কোনো আইটেম খুঁজে পাওয়া যায়নি!']);
                return;
            }
            
            $addedItems = [];
            foreach ($items as $item) {
                $name = trim($item['name']);
                $quantity = trim($item['quantity']);
                $category = $this->detectCategory($name);
                
                $this->listModel->addItem($user_id, $name, $quantity, $category);
                $addedItems[] = $name . ($quantity ? ' (' . $quantity . ')' : '');
            }
            
            echo json_encode([
                'success' => true,
                'message' => count($addedItems) . ' টি আইটেম যোগ হয়েছে!',
                'items' => $addedItems
            ]);
        }
    }

   
     */
    private function convertBengaliNumber($text) {
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($bn, $en, $text);
    }

    /**
     * কথ্য সংখ্যা শব্দ এবং বাংলা ভগ্নাংশ শব্দ (দেড়, সোয়া, পৌনে, আড়াই) কে ডিজিটে কনভার্ট করে।
     * এগুলো বাজারে অত্যন্ত কমন — "দেড় কেজি আলু", "সোয়া কেজি চাল", "পৌনে ২ কেজি" ইত্যাদি।
     * লম্বা phrase আগে match করানো হয়েছে যাতে ছোট শব্দ ভুলভাবে আগে বসে না যায়।
     */
    private function wordToNumber($text) {
        $wordNumbers = [
            // ভগ্নাংশ / আনুমানিক পরিমাণ বোঝানোর কথ্য শব্দ (বাজারে খুবই প্রচলিত)
            'সাড়ে তিন' => '3.5', 'সাড়ে চার' => '4.5', 'সাড়ে পাঁচ' => '5.5',
            'সাড়ে ছয়' => '6.5', 'সাড়ে সাত' => '7.5', 'সাড়ে আট' => '8.5',
            'সাড়ে নয়' => '9.5', 'সাড়ে দশ' => '10.5',
            'আড়াই' => '2.5',        // ২.৫
            'দেড়' => '1.5',         // ১.৫
            'সোয়া' => '1.25',       // সোয়া = ১ + ১/৪ (একক ইউনিটের সাথে ব্যবহৃত হলে)
            'পৌনে' => '0.75',       // পৌনে = ৩/৪ (একক ইউনিটের সাথে ব্যবহৃত হলে)
            'আধা' => '0.5', 'অর্ধেক' => '0.5', 'হাফ' => '0.5', 'অর্ধ' => '0.5',

            // "X টা/টি" যুক্ত সংখ্যা শব্দ (লম্বা আগে)
            'একটা' => '1', 'একটি' => '1',
            'দুইটা' => '2', 'দুইটি' => '2', 'দুটা' => '2', 'দুটো' => '2',
            'তিনটা' => '3', 'তিনটি' => '3',
            'চারটা' => '4', 'চারটি' => '4',
            'পাঁচটা' => '5', 'পাঁচটি' => '5',
            'ছয়টা' => '6', 'ছয়টি' => '6',
            'সাতটা' => '7', 'সাতটি' => '7',
            'আটটা' => '8', 'আটটি' => '8',
            'নয়টা' => '9', 'নয়টি' => '9',
            'দশটা' => '10', 'দশটি' => '10',

            // ঐতিহ্যবাহী গণনা একক (এখনো গ্রামাঞ্চলে/ফল-সবজিতে ব্যবহার হয়)
            'এক হালি' => '4', 'এক ডজন' => '12', 'এক কুড়ি' => '20', 'এক গন্ডা' => '4',

            // একক সংখ্যা শব্দ (এক ডিজিট, সবার শেষে match করাও)
            'এগারো' => '11', 'বারো' => '12', 'তেরো' => '13', 'চৌদ্দ' => '14',
            'পনেরো' => '15', 'ষোল' => '16', 'সতেরো' => '17', 'আঠারো' => '18',
            'উনিশ' => '19', 'বিশ' => '20',
            'এক' => '1', 'দুই' => '2', 'তিন' => '3', 'চার' => '4',
            'পাঁচ' => '5', 'ছয়' => '6', 'সাত' => '7', 'আট' => '8',
            'নয়' => '9', 'দশ' => '10',
        ];
        foreach ($wordNumbers as $word => $num) {
            $text = str_replace($word, ' ' . $num . ' ', $text);
        }
        return $text;
    }

    /**
     * ★★★ কেন্দ্রীয় ইউনিট ম্যাপ ★★★
     * প্রতিটা ইউনিটের জন্য canonical (স্ট্যান্ডার্ড ডিসপ্লে) নামের বিপরীতে
     * বাংলাদেশে মানুষ যত ধরনের ভাবে (বাংলা বানান ভেদে / ইংরেজি / সংক্ষেপে / ভুল বানানে)
     * বলতে পারে তার সবগুলো ভ্যারিয়েন্ট এখানে আছে। নতুন ইউনিট/ভ্যারিয়েন্ট লাগলে শুধু
     * এখানে যোগ করলেই পুরো সিস্টেমে কাজ করবে — আর কোথাও change লাগবে না।
     */
    private function getUnitMap() {
        return [
            // ---- ওজন (Weight) ----
            'কেজি' => ['কেজি', 'কেজী', 'কিলো', 'কিলোগ্রাম', 'কিলোগ্ৰাম', 'কিলোজি', 'কিজি', 'kg', 'kgs', 'kilo', 'kilos', 'kilogram', 'kilograms'],
            'গ্রাম' => ['গ্রাম', 'গ্ৰাম', 'গ্রামস', 'গরাম', 'gram', 'grams', 'gm', 'gms', 'g'],
            'পোয়া' => ['পোয়া', 'পোয়া কেজি'], // ≈ ২৫০ গ্রাম, বাজারে খুব কমন
            'সের' => ['সের', 'শের'],           // ঐতিহ্যবাহী ওজন একক (≈৯৩৩ গ্রাম)
            'ছটাক' => ['ছটাক', 'ছটাক্'],
            'মণ' => ['মণ', 'মন', 'মনি'],       // পাইকারি ওজন একক (≈৩৭.৩ কেজি)
            'ভরি' => ['ভরি', 'ভরী'],           // সোনা/গহনার একক

            // ---- আয়তন (Volume) ----
            'লিটার' => ['লিটার', 'লিটর', 'লিটারস', 'litre', 'litres', 'liter', 'liters', 'l'],
            'মিলি' => ['মিলি', 'মিলিলিটার', 'মিলিলিটর', 'ml', 'mls', 'millilitre', 'milliliter'],

            // ---- সংখ্যা / গণনা (Count) ----
            'টি' => ['টি', 'টা', 'পিস', 'পিছ', 'পিছে', 'piece', 'pieces', 'pcs', 'pc'],
            'ডজন' => ['ডজন', 'dozen', 'dozens'],
            'হালি' => ['হালি'],                 // = ৪ পিস (ডিম, ফল ইত্যাদির জন্য কমন)
            'গন্ডা' => ['গন্ডা', 'গণ্ডা'],       // ঐতিহ্যবাহী একক = ৪ পিস
            'কুড়ি' => ['কুড়ি'],                 // ঐতিহ্যবাহী একক = ২০ পিস
            'জোড়া' => ['জোড়া', 'জোড়', 'pair', 'pairs'],
            'সেট' => ['সেট', 'set', 'sets'],

            // ---- প্যাকেজিং (Packaging) ----
            'প্যাকেট' => ['প্যাকেট', 'প্যাকেটস', 'প্যাকট', 'packet', 'packets', 'pack', 'packs'],
            'বোতল' => ['বোতল', 'বোতলস', 'bottle', 'bottles'],
            'বস্তা' => ['বস্তা', 'sack', 'sacks'],
            'বাক্স' => ['বাক্স', 'বক্স', 'box', 'boxes'],
            'কার্টুন' => ['কার্টুন', 'carton', 'cartons'],
            'ব্যাগ' => ['ব্যাগ', 'bag', 'bags'],
            'ঝুড়ি' => ['ঝুড়ি', 'basket', 'baskets'],
            'টিন' => ['টিন', 'tin', 'tins'],
            'ড্রাম' => ['ড্রাম', 'drum', 'drums'],
            'বান্ডিল' => ['বান্ডিল', 'bundle', 'bundles'],

            // ---- রান্নাঘরের একক (Kitchen measures) ----
            'কাপ' => ['কাপ', 'cup', 'cups'],
            'চামচ' => ['চামচ', 'চা চামচ', 'টেবিল চামচ', 'spoon', 'spoons', 'tsp', 'tbsp'],

            // ---- সবজি/ফলের প্রচলিত বাজার-একক ----
            'আঁটি' => ['আঁটি', 'আটি'],          // শাকের আঁটি
            'কাঁদি' => ['কাঁদি', 'কান্দি'],       // কলার কাঁদি
            'ছড়া' => ['ছড়া'],                   // ফলের ছড়া
            'মুঠো' => ['মুঠো', 'মুঠ'],           // হাতের মুঠো পরিমাণ
        ];
    }

    /**
     * সব ভ্যারিয়েন্ট বানানকে canonical (স্ট্যান্ডার্ড) বাংলা ইউনিটে normalize করে,
     * যাতে আউটপুটে সবসময় একই ধরনের ইউনিট নাম দেখায় (যেমন "kg", "কিলো", "কেজী" —
     * সবই দেখাবে "কেজি")। বাংলা ভ্যারিয়েন্ট str_replace দিয়ে, আর ইংরেজি/ল্যাটিন
     * ভ্যারিয়েন্ট \b word-boundary regex দিয়ে (যাতে অন্য শব্দের ভেতরের অংশ ভুলভাবে
     * replace না হয়)।
     */
    private function normalizeUnits($text) {
        $unitMap = $this->getUnitMap();
        foreach ($unitMap as $canonical => $variants) {
            // লম্বা ভ্যারিয়েন্ট আগে replace করাও, নাহলে ছোট ভ্যারিয়েন্ট আগেই ম্যাচ করে
            // লম্বাটার একটা অংশ ভেঙে ফেলতে পারে (যেমন "kilogram" এর আগে "kg" ম্যাচ করলে সমস্যা)
            usort($variants, function ($a, $b) {
                return mb_strlen($b) - mb_strlen($a);
            });
            foreach ($variants as $variant) {
                if ($variant === $canonical) continue;
                if (preg_match('/^[a-zA-Z]+$/', $variant)) {
                    // ইংরেজি/ল্যাটিন ভ্যারিয়েন্ট — word boundary + case-insensitive
                    $text = preg_replace('/\b' . preg_quote($variant, '/') . '\b/i', ' ' . $canonical . ' ', $text);
                } else {
                    // বাংলা ভ্যারিয়েন্ট
                    $text = str_replace($variant, $canonical, $text);
                }
            }
        }
        return $text;
    }
    
    /**
     * 🧠 সুপার স্মার্ট এক্সট্র্যাক্টর
     * যেকোনো ধরনের বাক্য থেকে নাম+পরিমাণ বের করে
     */
    private function extractItemsWithQuantity($text) {
        // ০. প্রথমেই সব সংখ্যা ও ইউনিট normalize করো
        $text = $this->convertBengaliNumber($text);   // বাংলা ডিজিট → ইংরেজি ডিজিট
        $text = $this->wordToNumber($text);            // কথ্য/ভগ্নাংশ সংখ্যা শব্দ → ডিজিট
        $text = $this->normalizeUnits($text);           // যেকোনো ইউনিট ভ্যারিয়েন্ট → canonical বাংলা ইউনিট

        // ১. কমা, 'আর', 'এবং', 'ও' ইত্যাদি দিয়ে ভাগ করো
        $text = str_replace([' এবং ', ' ও ', ' আর ', ' তারপর '], ', ', $text);
        $text = str_replace(['।', '?', '!'], ',', $text);
        
        // ২. অপ্রয়োজনীয় শব্দ বাদ দাও (বাংলা ক্রিয়া, সহায়ক শব্দ)
        $removeWords = [
            'আমি', 'কিনব', 'কিনি', 'কিনসি', 'কিনছি', 'কিনলাম', 'কিনেছি', 'কিনমু',
            'চাই', 'দরকার', 'নাই', 'না', 'এখন', 'আজ', 
            'বাজার', 'থেকে', 'নিছি', 'নিসি', 'নিলাম', 'নিচি', 'নিলো', 'নেওয়া',
            'দিয়েছি', 'দিলাম', 'দিলো', 'দিচ্ছি', 'করব', 'করলাম', 'করেছি',
            'লাগবে', 'হবে', 'লাগে', 'রাখো', 'রাখেন', 'নিব', 'নেব', 'নিবো'
        ];
        foreach ($removeWords as $word) {
            $text = str_replace($word, '', $text);
        }
        
        // ৩. কমা দিয়ে স্প্লিট
        $parts = explode(',', $text);
        $items = [];
        
        // ৪. রেজেক্সের জন্য canonical ইউনিট লিস্ট (normalizeUnits এর পর সব canonical হয়ে গেছে)
        $units = array_keys($this->getUnitMap());
        // লম্বা ইউনিট নাম আগে ম্যাচ করানোর জন্য দৈর্ঘ্য অনুযায়ী সাজানো
        usort($units, function ($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });
        
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part) || mb_strlen($part) < 2) continue; // mb_strlen: বাংলা মাল্টিবাইট ক্যারেক্টার ঠিকভাবে গোনার জন্য
            
            $quantity = '';
            $name = $part;
            
            // ৫. সংখ্যা + ইউনিট খোঁজো (যেমন: "৫ টা", "১ ডজন", "২ কেজি", দশমিকসহ "১.৫ কেজি")
            preg_match('/(\d+(?:\.\d+)?)\s*(' . implode('|', $units) . ')/iu', $part, $matches);
            if (!empty($matches)) {
                $quantity = $matches[1] . ' ' . $matches[2]; // যেমন "৫ টা"
                // নাম থেকে পরিমাণ বাদ দাও
                $name = str_replace($matches[0], '', $part);
                $name = trim($name);
            } else {
                // ৬. শুধু সংখ্যা থাকলে (ইউনিট ছাড়া) যেমন "৫ আলু"
                preg_match('/(\d+(?:\.\d+)?)\s*/', $part, $numMatches);
                if (!empty($numMatches)) {
                    $quantity = $numMatches[1] . ' টি'; // ডিফল্ট ইউনিট "টি"
                    $name = str_replace($numMatches[0], '', $part);
                    $name = trim($name);
                }
            }
            
            // ৭. নাম থেকে অবশিষ্ট সংখ্যা/ইউনিট পরিষ্কার করো
            $name = preg_replace('/\d+(?:\.\d+)?\s*(' . implode('|', $units) . ')/iu', '', $name);
            $name = trim($name);
            
            // ৮. নাম ফাঁকা হলে পুরো অংশটাই নাম
            if (empty($name)) {
                $name = $part;
                $name = preg_replace('/\d+(?:\.\d+)?/', '', $name);
                $name = trim($name);
                $quantity = ''; // পরিমাণ না থাকলে ফাঁকা
            }
            
            // ৯. নাম থেকে 'টি', 'টা' ইত্যাদি যদি শেষে থাকে, সেটা বাদ দাও
            $name = preg_replace('/\s*(টি|টা|পিস|ডজন)\s*$/u', '', $name);
            $name = trim($name);
            
            // ১০. ফাইনাল চেক
            if (!empty($name) && mb_strlen($name) > 1) {
                $items[] = [
                    'name' => $name,
                    'quantity' => $quantity
                ];
            }
        }
        
        // ১১. ডুপ্লিকেট রিমুভ (যদি একই নাম বারবার আসে)
        $uniqueItems = [];
        $seen = [];
        foreach ($items as $item) {
            $key = $item['name'] . '|' . $item['quantity'];
            if (!in_array($key, $seen)) {
                $seen[] = $key;
                $uniqueItems[] = $item;
            }
        }
        
        return $uniqueItems;
    }
    
    private function detectCategory($item) {
        $vegetables = [
            'আলু', 'পেঁয়াজ', 'রসুন', 'আদা', 'টমেটো', 'শসা', 'কুমড়ো', 'লাউ', 
            'চালকুমড়ো', 'বেগুন', 'করলা', 'পটল', 'ঢেঁড়স', 'চিচিঙ্গা', 'ঝিঙা', 
            'কাঁচকলা', 'পেঁপে', 'শিম', 'বরবটি', 'সজনে', 'ফুলকপি', 'বাঁধাকপি', 
            'লেটুস', 'পাটশাক', 'কলমি', 'লালশাক', 'মুলা', 'গাজর', 'কপি', 'সিম',
            'মিষ্টি কুমড়ো', 'ওল', 'শালগম', 'বিট', 'পানিকচু', 'চুকা', 'নটে'
        ];
        
        $fish_meat = [
            'মাছ', 'মাংস', 'গরু', 'খাসি', 'মুরগি', 'রুই', 'কাতলা', 'ইলিশ', 
            'পাংগাস', 'চিংড়ি', 'শিং', 'মাগুর', 'কই', 'বোয়াল', 'পাবদা', 'গলদা', 
            'বাগদা', 'হাঁস', 'পার্ট', 'কোয়েল', 'টার্কি', 'ল্যাম্ব', 'চিকেন'
        ];
        
        $groceries = [
            'তেল', 'চাল', 'আটা', 'ময়দা', 'সুজি', 'ডাল', 'মসলা', 'লবণ', 
            'চিনি', 'গুড়', 'সয়াবিন', 'সরিষা', 'গুল', 'পিঁয়াজ', 'হলুদ', 
            'মরিচ', 'ধনে', 'জিরা', 'দারুচিনি', 'এলাচ', 'লবঙ্গ', 'চা', 'কফি', 'বিস্কুট'
        ];
        
        $fruits = [
            'আম', 'কলা', 'আপেল', 'কমলা', 'লেবু', 'পেয়ারা', 'তরমুজ', 
            'খেজুর', 'আনারস', 'লিচু', 'কাঁঠাল', 'স্ট্রবেরি', 'ড্রাগন', 
            'মালটা', 'জাম্বুরা', 'সফেদা', 'বেল', 'আতা', 'কামরাঙ্গা'
        ];
        
        $dairy = [
            'দুধ', 'ডিম', 'দই', 'ঘি', 'পনির', 'মাখন', 'ছানা', 'ক্রিম'
        ];
        
        foreach ($vegetables as $keyword) {
            if (strpos($item, $keyword) !== false) return 'সবজি';
        }
        foreach ($fish_meat as $keyword) {
            if (strpos($item, $keyword) !== false) return 'মাছ-মাংস';
        }
        foreach ($groceries as $keyword) {
            if (strpos($item, $keyword) !== false) return 'মুদি';
        }
        foreach ($fruits as $keyword) {
            if (strpos($item, $keyword) !== false) return 'ফল';
        }
        foreach ($dairy as $keyword) {
            if (strpos($item, $keyword) !== false) return 'দুগ্ধজাত';
        }
        
        return 'অন্যান্য';
    }
}
