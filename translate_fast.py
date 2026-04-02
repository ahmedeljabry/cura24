import json
import time
import sys
import concurrent.futures
from deep_translator import GoogleTranslator

sys.stdout.reconfigure(line_buffering=True)

def translate_chunk(chunk, target_lang):
    translator = GoogleTranslator(source='en', target=target_lang)
    translated_values = []
    joined = " <br> ".join([v.replace('<br>', ' ').strip() if v.strip() else 'EMPTY_STRING' for v in chunk])
    try:
        res = translator.translate(joined)
        if res:
            parts = [p.strip() for p in res.split('<br>')]
            if len(parts) == len(chunk):
                for idx, p in enumerate(parts):
                    if chunk[idx].strip() == '':
                        translated_values.append('')
                    else:
                        translated_values.append(p.replace('EMPTY_STRING', ''))
            else:
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
        for item in chunk:
            if not item.strip():
                translated_values.append('')
            else:
                try:
                    translated_values.append(translator.translate(item))
                except:
                    translated_values.append(item)
    return translated_values

def batch_translate(values, target_lang):
    chunks = [values[i:i+50] for i in range(0, len(values), 50)]
    translated_values = []
    
    with concurrent.futures.ThreadPoolExecutor(max_workers=5) as executor:
        futures = {executor.submit(translate_chunk, chunk, target_lang): i for i, chunk in enumerate(chunks)}
        results = [None] * len(chunks)
        
        for future in concurrent.futures.as_completed(futures):
            i = futures[future]
            try:
                results[i] = future.result()
                print(f"[{target_lang}] Completed batch {i+1}/{len(chunks)}")
            except Exception as e:
                print(f"[{target_lang}] Error in batch {i+1}: {e}")
                results[i] = chunks[i]
                
    for r in results:
        translated_values.extend(r)
        
    return translated_values

def process_file(source_file, target_file, lang_code):
    with open(source_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
        
    keys = list(data.keys())
    values = [str(data[k]) for k in keys]
    
    print(f"Translating {len(keys)} items to {lang_code}...")
    translated_vals = batch_translate(values, lang_code)
    
    new_data = {}
    for i, k in enumerate(keys):
        new_data[k] = translated_vals[i] if i < len(translated_vals) else data[k]
        
    with open(target_file, 'w', encoding='utf-8') as f:
        json.dump(new_data, f, ensure_ascii=False, indent=4)
    print(f"Saved {target_file}")

process_file('resources/lang/default.json', 'resources/lang/ar.json', 'ar')
process_file('resources/lang/default.json', 'resources/lang/it.json', 'it')
print("All done!")
