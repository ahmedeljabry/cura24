import json
from deep_translator import GoogleTranslator

# Subset of keys common to Dashboard
keys_to_translate = [
    "Seller Dashboard",
    "Buyer Dashboard",
    "Home",
    "View Site",
    "Dashboard",
    "Manage your accounts activity from here",
    "Notifications",
    "View all",
    "Clear all",
    "Profile",
    "Settings",
    "Logout",
    "Total Order",
    "Pending",
    "Active",
    "Completed",
    "Delivered",
    "Cancel",
    "Recent Order",
    "Order type:",
    "Order status:",
    "Order amount:",
    "Order In Progress",
    "Order Pending",
    "Order Completed",
    "Total Withdraw",
    "Remaining Balance",
    "No New Notification",
    "Earning",
    "Total Buyer",
    "To Do List",
    "See All"
]

def process_file(source_file, target_file, lang_code):
    with open(source_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
        
    translator = GoogleTranslator(source='en', target=lang_code)
    print(f"Translating {len(keys_to_translate)} items to {lang_code}...")
    
    new_data = dict(data)
    for k in keys_to_translate:
        if k in new_data:
            try:
                new_data[k] = translator.translate(k)
                print(f"Translated '{k}' -> '{new_data[k]}'")
            except e:
                print(f"Failed to translate {k}: {e}")
                
    with open(target_file, 'w', encoding='utf-8') as f:
        json.dump(new_data, f, ensure_ascii=False, indent=4)
    print(f"Saved {target_file}")

process_file('resources/lang/default.json', 'resources/lang/ar.json', 'ar')
process_file('resources/lang/default.json', 'resources/lang/it.json', 'it')
print("All done!")
