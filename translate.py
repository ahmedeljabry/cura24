import json
import time
from deep_translator import GoogleTranslator

def batch_translate(values, target_lang):
    translator = GoogleTranslator(source='en', target=target_lang)
    translated_values = []
    
    for i in range(0, len(values), 50): # batch 50 items
        chunk = values[i:i+50]
        # create a payload using a rare separator that Google preserves
        joined = " <br> ".join([v.replace('<br>', ' ').strip() if v.strip() else 'EMPTY_STRING' for v in chunk])
        try:
            res = translator.translate(joined)
            if res:
                # Some spaces might be added around <br>
                parts = [p.strip() for p in res.split('<br>')]
                if len(parts) == len(chunk):
                    for idx, p in enumerate(parts):
                        if chunk[idx].strip() == '':
                            translated_values.append('')
                        else:
                            translated_values.append(p.replace('EMPTY_STRING', ''))
                else:
                    # fallback to individual
                    for item in chunk:
                        if not item.strip():
                            translated_values.append('')
                        else:
                            try:
                                translated_values.append(translator.translate(item))
                            except:
                                translated_values.append(item)
            else:
                translated_values.extend(chunk)
        except Exception as e:
            # fallback
            for item in chunk:
                if not item.strip():
                    translated_values.append('')
                else:
                    try:
                        translated_values.append(translator.translate(item))
                    except:
                        translated_values.append(item)
                        
        print(f"Translated {min(i+50, len(values))}/{len(values)}")
        time.sleep(1)
        
    return translated_values

def process_file(source_file, target_file, lang_code):
    with open(source_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
        
    keys = list(data.keys())
    values = [str(data[k]) for k in keys]
    
    print(f"\nTranslating {len(keys)} items to {lang_code}...")
    translated_vals = batch_translate(values, lang_code)
    
    new_data = {}
    for i, k in enumerate(keys):
        new_data[k] = translated_vals[i] if i < len(translated_vals) else data[k]
        
    with open(target_file, 'w', encoding='utf-8') as f:
        json.dump(new_data, f, ensure_ascii=False, indent=4)
    print(f"Saved {target_file}")

process_file('resources/lang/default.json', 'resources/lang/ar.json', 'ar')
process_file('resources/lang/default.json', 'resources/lang/it.json', 'it')
