/**
 * Giải mã văn bản Tai Viet gõ bằng font-hack ASCII ("Thai VietNam chuan/co") sang Unicode.
 * Bản TypeScript của scripts/fonthack/decode.py — kết quả phải giống từng ký tự
 * (kiểm bằng js/fonthack/parity.test.ts). Không phụ thuộc thư viện nào.
 *
 * Tóm tắt thuật toán (chi tiết: CLAUDE.md, mục font-hack):
 *  1. Tra bảng byte -> Unicode (byte không có trong bảng: chữ số, dấu câu... giữ nguyên).
 *  2. Font cũ gõ DẤU trước phụ âm; Unicode cần phụ âm trước -> reorder().
 *  3. NFC + sắp chùm dấu theo (ccc, codepoint) -> cùng quy ước với từ điển.
 */

export const LEGACY_FONTS = ['Thai VietNam chuan', 'Thai VietNam co'] as const;

// Người bản ngữ đã xác nhận (2026-09-27). Chữ thường = phụ âm THẤP, chữ hoa = CAO.
export const TABLE: Readonly<Record<string, string>> = {
  'A': 'ꪹꪷ', // VOWEL UEA + MAI KHIT (glyph ghép)
  'a': 'ꪱ', // VOWEL AA
  'B': 'ꪛ', 'b': 'ꪚ', // BO
  'C': 'ꪁ', 'c': 'ꪀ', // KO
  'D': 'ꪓ', 'd': 'ꪒ', // DO
  'E': 'ꪹ', // VOWEL UEA
  'e': 'ꪵ', // VOWEL E
  'F': 'ꪡ', 'f': 'ꪠ', // FO
  'G': 'ꪉ', 'g': 'ꪈ', // NGO
  'H': 'ꪭ', 'h': 'ꪬ', // HO
  'I': 'ꪸ', // VOWEL IA
  'i': 'ꪲ', // VOWEL I
  'J': 'ꪥ', 'j': 'ꪤ', // YO
  'K': 'ꪅ', 'k': 'ꪄ', // KHHO
  'L': 'ꪩ', 'l': 'ꪨ', // LO
  'M': 'ꪣ', 'm': 'ꪢ', // MO
  'N': 'ꪙ', 'n': 'ꪘ', // NO
  'O': 'ꪯ', 'o': 'ꪮ', // O
  'P': 'ꪝ', 'p': 'ꪜ', // PO
  'Q': 'ꫜ', // SYMBOL NUENG
  'q': 'ꫛ', // SYMBOL KON
  'R': 'ꪧ', 'r': 'ꪦ', // RO
  'S': 'ꪋ', 's': 'ꪊ', // CO
  'T': 'ꪕ', 't': 'ꪔ', // TO
  'U': 'ꪳ', // VOWEL UE
  'u': 'ꪴ', // VOWEL U
  'V': 'ꪫ', 'v': 'ꪪ', // VO
  'W': 'ꪗ', 'w': 'ꪖ', // THO
  'X': 'ꪏ', 'x': 'ꪎ', // SO
  'Y': 'ꪻ', // VOWEL AUE
  'y': 'ꪼ', // VOWEL AY
  'Z': 'ꪑ', 'z': 'ꪐ', // NYO
  '#': 'ꪹꪸ', // VOWEL UEA + VOWEL IA (glyph ghép; người bản ngữ xác nhận)
  '$': 'ꪽ', // VOWEL AN
  '&': '꪿', // TONE MAI EK
  '*': '꫁', // TONE MAI THO
  '<': 'ꪶ', // VOWEL O
  '>': 'ꪺ', // VOWEL UA
  '[': 'ꪷ', // MAI KHIT
  '\\': 'ꪆ', // LOW GO
  ']': 'ꫝ', // SYMBOL SAM
  '^': '꫟', // SYMBOL KOI KOI
  '`': '', // hoa văn riêng của font, không phải chữ -> bỏ
  '{': 'ꪾ', // VOWEL AM
  '|': 'ꪇ', // HIGH GO
  '}': 'ꪰ', // MAI KANG
  '~': '꫞', // SYMBOL HO HOI
};

/** Byte từng kém chắc chắn nhất — gắn cờ để truy vết nếu phát hiện lỗi. */
export const UNSURE = new Set(['O', 'w', 'g', 'D']);

// 9 dấu tổ hợp Tai Viet và ccc của chúng (JS không có API ccc như unicodedata.combining).
// Không ký tự Tai Viet nào có phân rã chuẩn, nên NFC không bao giờ đụng tới chúng;
// thứ tự chùm dấu do canon() quyết định.
const CCC: Readonly<Record<string, number>> = {
  'ꪰ': 230, // MAI KANG
  'ꪲ': 230, // VOWEL I
  'ꪳ': 230, // VOWEL UE
  'ꪴ': 220, // VOWEL U
  'ꪷ': 230, // MAI KHIT
  'ꪸ': 230, // VOWEL IA
  'ꪾ': 230, // VOWEL AM
  '꪿': 230, // TONE MAI EK
  '꫁': 230, // TONE MAI THO
};
const isMark = (ch: string): boolean => ch in CCC;
const isCons = (ch: string): boolean => {
  const u = ch.codePointAt(0)!;
  return u >= 0xaa80 && u <= 0xaaaf;
};

/**
 * Đưa mọi dấu về sau phụ âm: gắn vào phụ âm liền trước (cùng từ); nếu chưa có
 * phụ âm nào thì giữ lại cho phụ âm kế tiếp.
 */
export function reorder(u: string): string {
  const out: string[] = [];
  let buf = '';
  for (const ch of u) {
    const lastIsCons = out.length > 0 && isCons([...out[out.length - 1]][0]);
    if (isMark(ch)) {
      if (lastIsCons) out[out.length - 1] += ch;
      else buf += ch;
    } else if (isCons(ch)) {
      out.push(ch + buf);
      buf = '';
    } else {
      if (buf && lastIsCons) {
        out[out.length - 1] += buf;
        buf = '';
      }
      out.push(ch);
    }
  }
  if (buf) {
    if (out.length) out[out.length - 1] += buf;
    else out.push(buf);
  }
  return out.join('');
}

/** NFC + sắp mỗi chùm dấu theo (ccc, codepoint) — giống tv_norm.visual_key. */
export function canon(s: string): string {
  const cps = [...s.normalize('NFC')];
  const out: string[] = [];
  let i = 0;
  while (i < cps.length) {
    out.push(cps[i]);
    let j = i + 1;
    while (j < cps.length && isMark(cps[j])) j++;
    out.push(
      ...cps.slice(i + 1, j).sort(
        (a, b) => CCC[a] - CCC[b] || a.codePointAt(0)! - b.codePointAt(0)!,
      ),
    );
    i = j;
  }
  return out.join('');
}

/** Chuỗi byte font-hack -> Unicode Tai Viet; xử lý từng từ để dấu không nhảy sang từ bên cạnh. */
export function decode(text: string): string {
  return text
    .split(' ')
    .map((w) => canon(reorder([...w].map((ch) => TABLE[ch] ?? ch).join(''))))
    .join(' ');
}

export function isLegacyFont(font: string): boolean {
  return LEGACY_FONTS.some((f) => font.includes(f));
}

export interface TextRun {
  trang: number; // số trang (PDF) hoặc 0
  top: number;
  left: number;
  font: string;
  raw: string;
}

export interface DecodedRun extends TextRun {
  ngon_ngu: 'tai' | 'khac';
  unicode: string;
  byte_chua_chac: string;
}

/** Giải mã các run dùng font legacy; run khác (tiếng Việt...) giữ nguyên. */
export function decodeRuns(runs: Iterable<TextRun>): DecodedRun[] {
  const res: DecodedRun[] = [];
  for (const r of runs) {
    const leg = isLegacyFont(r.font);
    res.push({
      ...r,
      ngon_ngu: leg ? 'tai' : 'khac',
      unicode: leg ? decode(r.raw) : r.raw,
      byte_chua_chac: leg ? [...new Set(r.raw)].filter((c) => UNSURE.has(c)).sort().join('') : '',
    });
  }
  return res;
}
