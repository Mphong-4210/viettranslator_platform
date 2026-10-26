<?php
//public_html/functions/PhienAmSangChuThai.php
mb_internal_encoding('UTF-8'); // Thiết lập mã hóa UTF-8
mb_regex_encoding('UTF-8'); // Đảm bảo regex hoạt động với Unicode

//Toàn bộ văn bản truyền vào sẽ được đưa về chữ thường trước khi xử lý ở hàm đích
//que.. qua.. đổi thành koe.. koa.., qui đổi thành kuy
//còn lại đổi thành ku..
//Ý tưởng:
//- Một từ chữ Thái sẽ bao gồm đẩy đủ hoặc không đầy đủ các thành phần: 1.Nguyên âm trước - 2.Phụ âm đầu - 3.Nguyên âm trên - 4.Dấu thanh điệu - 5.Nguyên âm sau - 6.Phụ âm cuối.
//- Ta sẽ tạo các hàm riêng biệt để phân tách để tìm ra các thành phần này sẽ đó ghép nối chúng lại (dựa vào hàm ánh xạ chữ phiên âm vs chữ Thái)

function LaChuThai($word) {
    for ($i = 0; $i < mb_strlen($word); $i++) {
        $char = mb_substr($word, $i, 1);
        $code = IntlChar::ord($char);
        if ($code < 0xAA80 || $code > 0xAADF) {
            return false;
        }
    }
    return true;
}
function DauNgangSangDauNga_PAsCT($text) {
    // Chuẩn hóa chuỗi về dạng Precomposed (NFC)
    if (class_exists('Normalizer')) {
        $text = Normalizer::normalize($text, Normalizer::FORM_C);
    }

    $map = [
        'ā' => 'ã', 'Ā' => 'Ã',
        'ē' => 'ẽ', 'Ē' => 'Ẽ',
        'ī' => 'ĩ', 'Ī' => 'Ĩ',
        'ō' => 'õ', 'Ō' => 'Õ',
        'ū' => 'ũ', 'Ū' => 'Ũ',
        'ȳ' => 'ỹ', 'Ȳ' => 'Ỹ',
        'ă̄' => 'ẵ', 'Ă̄' => 'Ẵ',
        'â̄' => 'ẫ', 'Â̄' => 'Ẫ',
        'ê̄' => 'ễ', 'Ê̄' => 'Ễ',
        'ô̄' => 'ỗ', 'Ô̄' => 'Ỗ',
        'ơ̄' => 'ỡ', 'Ỡ' => 'Ơ̄',
        'ư̄' => 'ữ', 'Ư̄' => 'Ữ'
    ];

    return strtr($text, $map);
}

function pBoDauThanhDieu($word) {
    $map = [
        // Nhỏ
        'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ắ' => 'ă', 'ằ' => 'ă', 'ẳ' => 'ă', 'ẵ' => 'ă', 'ặ' => 'ă',
        'ấ' => 'â', 'ầ' => 'â', 'ẩ' => 'â', 'ẫ' => 'â', 'ậ' => 'â',
        'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ế' => 'ê', 'ề' => 'ê', 'ể' => 'ê', 'ễ' => 'ê', 'ệ' => 'ê',
        'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ố' => 'ô', 'ồ' => 'ô', 'ổ' => 'ô', 'ỗ' => 'ô', 'ộ' => 'ô',
        'ớ' => 'ơ', 'ờ' => 'ơ', 'ở' => 'ơ', 'ỡ' => 'ơ', 'ợ' => 'ơ',
        'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ứ' => 'ư', 'ừ' => 'ư', 'ử' => 'ư', 'ữ' => 'ư', 'ự' => 'ư',
        'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',

        // Hoa
        'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A',
        'Ắ' => 'Ă', 'Ằ' => 'Ă', 'Ẳ' => 'Ă', 'Ẵ' => 'Ă', 'Ặ' => 'Ă',
        'Ấ' => 'Â', 'Ầ' => 'Â', 'Ẩ' => 'Â', 'Ẫ' => 'Â', 'Ậ' => 'Â',
        'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E',
        'Ế' => 'Ê', 'Ề' => 'Ê', 'Ể' => 'Ê', 'Ễ' => 'Ê', 'Ệ' => 'Ê',
        'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O',
        'Ố' => 'Ô', 'Ồ' => 'Ô', 'Ổ' => 'Ô', 'Ỗ' => 'Ô', 'Ộ' => 'Ô',
        'Ớ' => 'Ơ', 'Ờ' => 'Ơ', 'Ở' => 'Ơ', 'Ỡ' => 'Ơ', 'Ợ' => 'Ơ',
        'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U',
        'Ứ' => 'Ư', 'Ừ' => 'Ư', 'Ử' => 'Ư', 'Ữ' => 'Ư', 'Ự' => 'Ư',
        'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y',
    ];
     // Chuẩn hóa về dạng NFC sau khi thay thế
    return normalizer_normalize(strtr($word, $map), Normalizer::FORM_C);
}
function mangNguyenAm(){
    return $nguyenAm = ['a', 'ă', 'â', 'e', 'ê', 'i', 'o', 'ô', 'ơ', 'u', 'ư', 'y',
                 'á','à','ả','ã','ạ','ắ','ằ','ẳ','ẵ','ặ','ấ','ầ','ẩ','ẫ','ậ',
                 'é','è','ẻ','ẽ','ẹ','ế','ề','ể','ễ','ệ',
                 'í','ì','ỉ','ĩ','ị',
                 'ó','ò','ỏ','õ','ọ','ố','ồ','ổ','ỗ','ộ','ớ','ờ','ở','ỡ','ợ',
                 'ú','ù','ủ','ũ','ụ','ứ','ừ','ử','ữ','ự',
                 'ý','ỳ','ỷ','ỹ','ỵ'];
}
// Thay ău => aư
function thayTheAw($vanBan) {
    $from_1 = ["ằu", "ắu", "ẳu", "ẵu", "ău", "ặu"];
    $from_2 = ["ằư", "ắư", "ẳư", "ẵư", "ăư", "ặư"];
    $to   = ["àư", "áư", "ảư", "ãư", "aư", "ạư"];
    return str_replace($from_2, $to, str_replace($from_1, $to, $vanBan));
}

function LaNguyenAm($MotKyTu){
    $mangNguyenAm = ['a', 'ă', 'â', 'e', 'ê', 'i', 'o', 'ô', 'ơ', 'u', 'ư', 'y',
                 'á', 'ắ', 'ấ', 'é', 'ế', 'í', 'ó', 'ố', 'ớ', 'ú', 'ứ', 'ý',
                 'à', 'ằ', 'ầ', 'è', 'ề', 'ì', 'ò', 'ồ', 'ờ', 'ù', 'ừ', 'ỳ',
                 'ả', 'ẳ', 'ẩ', 'ẻ', 'ể', 'ỉ', 'ỏ', 'ổ', 'ở', 'ủ', 'ử', 'ỷ',
                 'ã', 'ẵ', 'ẫ', 'ẽ', 'ễ', 'ĩ', 'õ', 'ỗ', 'ỡ', 'ũ', 'ữ', 'ỹ',
                 'ạ', 'ặ', 'ậ', 'ẹ', 'ệ', 'ị', 'ọ', 'ộ', 'ợ', 'ụ', 'ự', 'ỵ'];
    if(in_array($MotKyTu, $mangNguyenAm)){
        $LaNguyenAm = true;
    }else{
        $LaNguyenAm = false;
    }
    return $LaNguyenAm;
}
function pToThanhDieu($word){
    $dauToThap = [
        'à', 'á', 'ả',
        'ằ', 'ắ', 'ẳ',
        'ầ', 'ấ', 'ẩ',
        'è', 'é', 'ẻ',
        'ề', 'ế', 'ể',
        'ò', 'ó', 'ỏ',
        'ồ', 'ố', 'ổ',
        'ờ', 'ớ', 'ở',
        'ù', 'ú', 'ủ',
        'ừ', 'ứ', 'ử',
        'ì', 'í', 'ỉ',
        'ỳ', 'ý', 'ỷ',
    ];

    for ($i = mb_strlen($word) - 1; $i >= 0; $i--) {
        $char = mb_substr($word, $i, 1);
        if (in_array($char, $dauToThap)) {
            return 1; // Tổ thấp
        }
    }
    return 2; // Nếu không có nguyên âm thuộc tổ thấp thì là tổ cao
}

function pThanhDieu($word){
    $dauHuyen = ['à','ằ','ầ','è','ề','ò','ồ','ờ','ù','ừ','ì','ỳ'];
    $dauSac   = ['á','ắ','ấ','é','ế','ó','ố','ớ','ú','ứ','í','ý'];
    $dauHoi   = ['ả','ẳ','ẩ','ẻ','ể','ỏ','ổ','ở','ủ','ử','ỉ','ỷ'];
    $dauNga   = ['ã','ẵ','ẫ','ẽ','ễ','õ','ỗ','ỡ','ũ','ữ','ĩ','ỹ'];
    $dauNang  = ['ạ','ặ','ậ','ẹ','ệ','ọ','ộ','ợ','ụ','ự','ị','ỵ'];

    for ($i = mb_strlen($word) - 1; $i >= 0; $i--) {
        $char = mb_substr($word, $i, 1);
        if (in_array($char, $dauHuyen)) return 1;
        if (in_array($char, $dauSac))   return 2;
        if (in_array($char, $dauHoi))   return 3;
        if (in_array($char, $dauNga))   return 4;
        if (in_array($char, $dauNang))  return 6;
    }
    return 5; // Không có dấu → thanh ngang
}
function pPhuAmCuoi($word){
    //Các phụ âm cuối: k1, 
    //Kiểm tra các từ có âm cuối v2, d2: Nếu ký tự áp chót là nguyên âm, đồng thời ký tự cuối là i, o, u thì ký tự cuối là phụ âm 
    //Các âm cuối tương ứng với ay, ăn, ăm, aư. Nếu có các âm cuối này thì từ không có phụ âm cuối  
    //Nếu ký tự cuối của từ là nguyên âm thì từ đó cũng không có phụ âm cuối
    //Xét hai ký tự cuối cùng, nếu ký tự áp chót là phụ âm nữa thì cả hai đều là phụ âm cuối. Nếu không thì ký tự cuối cùng chính là phụ âm cuối
    
    // Bảng ánh xạ chữ cái Thái Việt Nam sang chữ Việt
    
                 
    $mangAmCuoiIOU = ['i', 'o', 'u'];
    $mangAmCuoiKep = ['au', 'âu','ay', 'ây', 'ăư', 'aư'];
    
    $TuNguBoDau = pBoDauThanhDieu($word);
    $SoKyTu = strlen($TuNguBoDau);
    
    $MotKyTuCuoi = mb_substr($TuNguBoDau, -1, 1, "UTF-8");//Tách lấy một ký tự cuối
    $MotKyTuApChot = mb_substr($TuNguBoDau, -2, 1, "UTF-8");//Tách lấy một ký tự áp chót
    $HaiKyTuCuoi = mb_substr($TuNguBoDau, -2, 2, "UTF-8");//Tách lấy hai ký tự cuối
    
    if($SoKyTu==1&&in_array($HaiKyTuCuoi, $mangAmCuoiIOU)){
        //nếu từ ngữ chỉ có một ký tự và ký tự đó lại là i, o, u thì từ ngữ này không có phụ âm cuối 
        $pPhuAmCuoi = "";
    }else if($HaiKyTuCuoi=='ăn' || $HaiKyTuCuoi=='ăm' || in_array($HaiKyTuCuoi, $mangAmCuoiKep)){
        $pPhuAmCuoi = "";
    }else if(LaNguyenAm($MotKyTuApChot)==true&&in_array($MotKyTuCuoi, $mangAmCuoiIOU)){
        $pPhuAmCuoi = $MotKyTuCuoi;
    }else if(LaNguyenAm($MotKyTuCuoi)==true){
        $pPhuAmCuoi = "";
    }elseif(LaNguyenAm($MotKyTuApChot)==false){
        $pPhuAmCuoi = $HaiKyTuCuoi;
    }else{
        $pPhuAmCuoi = $MotKyTuCuoi;
    }
    return $pPhuAmCuoi;
}
function pPhuAmDau($word){
    $MotKyTuDau = mb_substr($word,0,1);
    $HaiKyTuDau = mb_substr($word,0,2);
    $BaKyTuDau = mb_substr($word,0,3);
    $mangPhuAmDoi = ['th', 'nh', 'ch', 'tr', 'kh', 'ph', 'ng'];
    $mangPhuAmBa = ['ngh', 'chh', 'khh', 'phh'];
    
    //Nếu chữ cái đầu tiên của từ là nguyên âm thì từ có phụ âm đầu rỗng
    //Kiểm tra từ có phụ âm ba: ngh, chh, khh, phh
    //Kiểm tra từ có phụ âm đôi: th, nh, ch, kh, ph
    //Còn lại 1 ký tự đầu chính là phụ âm đầu
    
    if(LaNguyenAm($MotKyTuDau)==true){
        $pPhuAmDau="";
    }else if(in_array($BaKyTuDau, $mangPhuAmBa)){
        $pPhuAmDau=$BaKyTuDau;
    }else if(in_array($HaiKyTuDau, $mangPhuAmDoi)){
        $pPhuAmDau=$HaiKyTuDau;
    }else{
        $pPhuAmDau=$MotKyTuDau;
    }
    
    return $pPhuAmDau;
}
function pBanAmVo($word){
    $pBoDau=pBoDauThanhDieu($word);
    // Danh sách tổ hợp có bán âm o
    $mangO = ['oa', 'oă', 'oe'];

    // Danh sách tổ hợp có bán âm u
    $mangU = ['uă', 'uê', 'uy', 'uơ'];

    // Kiểm tra xem từ có chứa tổ hợp nào thuộc mảng bán âm o không
    foreach ($mangO as $item) {
        if (mb_strpos($pBoDau, $item) !== false) {
            return 'o';
        }
    }

    // Kiểm tra xem từ có chứa tổ hợp nào thuộc mảng bán âm u không
    foreach ($mangU as $item) {
        if (mb_strpos($pBoDau, $item) !== false) {
            return 'u';
        }
    }

    // Nếu không tìm thấy thì trả về rỗng
    return '';
}
function pPhuAmSau($word){
    $pBoDau = pBoDauThanhDieu($word);
    $soPhuAmDau = mb_strlen(pPhuAmDau($word));
    $soPhuAmCuoi = mb_strlen(pPhuAmCuoi($word));
    
    $HaiKyTuCuoi = mb_substr($pBoDau,-2,2);
    
    //Loại bỏ phụ âm đầu, phụ âm cuối
    //Loại bỏ nguyên âm
    //Phụ âm đầu tiên trong dãy còn lại là phụ âm sau (thường dãy chỉ còn 1 phụ âm)
    
    //Lọc bỏ nguyên âm của dãy còn lại 
    //Phụ âm còn lại nếu có 2 phụ âm trở lên thì chỉ lọc lấy 1 phụ âm đầu tiên hoặc là 2 nếu là phụ âm đôi
    //Nếu âm cuối là ăn hoặc ăm thì bỏ 1 ký tự cuối
    
    $boPhuAmDau = mb_substr($word, $soPhuAmDau, null, 'UTF-8'); // Bắt đầu từ ký tự thứ $soPhuAmDau+1, lấy đến hết
    if($HaiKyTuCuoi=='ăn' || $HaiKyTuCuoi=='ăm'){
        $boPhuAmSau = mb_substr($boPhuAmDau, 0, mb_strlen($boPhuAmDau, 'UTF-8') - 1, 'UTF-8');
    }else{
        $boPhuAmSau = mb_substr($boPhuAmDau, 0, mb_strlen($boPhuAmDau, 'UTF-8') - $soPhuAmCuoi, 'UTF-8');
    }
    
    $tu = mb_strtolower($boPhuAmSau, 'UTF-8'); // chuẩn hóa về chữ thường
    $giuPhuAm = '';

    // Duyệt từng ký tự trong từ
    for ($i = 0; $i < mb_strlen($tu, 'UTF-8'); $i++) {
        $kyTu = mb_substr($tu, $i, 1, 'UTF-8');
        if (!in_array($kyTu, mangNguyenAm())) {
            $giuPhuAm .= $kyTu; // Giữ lại nếu không phải nguyên âm
        }
    }
    $giu1PhuAm = mb_substr($giuPhuAm,0,1);
    $giu2PhuAm = mb_substr($giuPhuAm,0,2);
    $giu3PhuAm = mb_substr($giuPhuAm,0,3);
    if($giu3PhuAm=='ngh'){
        $pPhuAmSau=$giu3PhuAm;
    }else if($giu2PhuAm=='th' || $giu2PhuAm=='ch' || $giu2PhuAm=='ng' || $giu2PhuAm=='nh' || $giu2PhuAm=='ph' || $giu2PhuAm=='kh'){
        $pPhuAmSau=$giu2PhuAm;
    }else{
        $pPhuAmSau=$giu1PhuAm;
    }
    
    return $pPhuAmSau;
}
function pNguyenAm($word){
    $pBoDau = pBoDauThanhDieu($word);
    $pPhuAmDau = pPhuAmDau($pBoDau);
    $pPhuAmSau = pPhuAmSau($pBoDau);
    $pBanAmVo = pBanAmVo($pBoDau);
    $pPhuAmCuoi = pPhuAmCuoi($pBoDau);
    if(empty($pPhuAmDau)&&!empty($pPhuAmSau)){
        $substring = strstr($pBoDau, $pPhuAmSau, true); // Lấy từ đầu đến hết 'l'
        $soPhuAmTruoc = mb_strlen($substring.$pPhuAmSau.$pBanAmVo); // Đếm số ký tự
    }else{
        $soPhuAmTruoc = mb_strlen($pPhuAmDau.$pPhuAmSau.$pBanAmVo);// Không tìm thấy 'l'
    }

    
    $soPhuAmCuoi = mb_strlen($pPhuAmCuoi);
    
    $boPhuAmTruoc = mb_substr($pBoDau, $soPhuAmTruoc, null, 'UTF-8'); // Bắt đầu từ ký tự thứ $soPhuAmTruoc, lấy đến hết
    $boPhuAmSau = mb_substr($boPhuAmTruoc, 0, mb_strlen($boPhuAmTruoc, 'UTF-8') - $soPhuAmCuoi, 'UTF-8');
    $MotNguyenAmSau = mb_substr($boPhuAmSau,-2,2);
    $HaiNguyenAmSau = mb_substr($boPhuAmSau,-2,2);
    $HaiKyTuSau = mb_substr($pBoDau,-2,2);
    //Lọc ra cụm nhiều hơn 2 nguyên âm
    $mangNguyenAmDon = ['ă', 'a', 'i', 'ư', 'u', 'e', 'ô', 'o', 'ê', 'ơ', 'â'];
    $mangNguyenAmDoi =['ia', 'iê', 'ưa', 'ươ', 'ua', 'uô', 'uâ', 'aư', 'ăư', 'ay', 'au','âu'];
    $mangNguyenAmDoi_AW = ['ăn', 'ăm', 'ăp'];
    if($HaiKyTuSau=='ăn'){
        $pNguyenAm='ăn';
    }else if($HaiKyTuSau=='ăm'||$HaiKyTuSau=='ăp'){
        $pNguyenAm='ăm';
    }else if(in_array($HaiNguyenAmSau, $mangNguyenAmDoi)){
        $pNguyenAm=$HaiNguyenAmSau;
    }else if(in_array($MotNguyenAmSau, $mangNguyenAmDon)){
        $pNguyenAm=$MotNguyenAmSau;
    }else{
        $pNguyenAm=$MotNguyenAmSau;
    }
    //vần anh đổi a thành e, nh sẽ đc chuyển thành ng2 ở tPhuAmCuoi()
    if(($pPhuAmCuoi=="nh"&&$pNguyenAm=="a")||($pPhuAmCuoi=="ch"&&$pNguyenAm=="a")){
        $pNguyenAm="e";
    }
    if(!in_array($pNguyenAm, $mangNguyenAmDoi_AW)){
        $pNguyenAm=mb_ereg_replace("[^aăâeêyuưioôơ]","",$pNguyenAm);
    }
    return $pNguyenAm;
}
function tPhuAmCuoi($word){
    $pPhuAmCuoi = pPhuAmCuoi($word);
    $AnhXaPhuAmCuoi = ['i' => 'ꪥ', 'o' => 'ꪫ', 'u' => 'ꪫ', 'c' => 'ꪀ', 'ch' => 'ꪀ', 'k' => 'ꪀ', 't' => 'ꪒ', 'p' => 'ꪚ', 'm' => 'ꪣ', 'n' => 'ꪙ', 'ng' => 'ꪉ','nh' => 'ꪉ',  ];
    
    return strtr($pPhuAmCuoi, $AnhXaPhuAmCuoi);
}
function tPhuAmDau($word) {
    // Bảng ánh xạ phiên âm → chữ cái Thái Việt Nam
    $pPhuAmDau_ = pPhuAmDau($word);
    if(empty($pPhuAmDau_)){
       $pPhuAmDau='o';
    }else{
        $pPhuAmDau=$pPhuAmDau_;
    }
    $ToThanhDieu = pToThanhDieu($word);
    $pPhuAmDau_12 = $pPhuAmDau.$ToThanhDieu;
    $mangAnhXaPhuAm = [
    'k1'   => 'ꪀ', 'k2'   => 'ꪁ',
    'c1'  => 'ꪀ', 'c2'   => 'ꪁ',
    'q1'  => 'ꪀ', 'q2'   => 'ꪁ',
    'khh1' => 'ꪂ', 'khh2' => 'ꪃ',
    'kh1'  => 'ꪄ', 'kh2'  => 'ꪅ',
    'g1'   => 'ꪆ', 'g2'   => 'ꪇ',
    'ng1'  => 'ꪈ', 'ng2'  => 'ꪉ',
    'ngh1'  => 'ꪈ', 'ngh2'  => 'ꪉ',
    'ch1'  => 'ꪊ', 'ch2'  => 'ꪋ',
    'tr1'  => 'ꪊ', 'tr2'  => 'ꪋ',
    'chh1' => 'ꪌ', 'chh2' => 'ꪍ',
    's1'   => 'ꪎ', 's2'   => 'ꪏ',
    'x1'   => 'ꪎ', 'x2'   => 'ꪏ',
    'nh1'  => 'ꪐ', 'nh2'  => 'ꪑ',
    'đ1'   => 'ꪒ', 'đ2'   => 'ꪓ',
    't1'   => 'ꪔ', 't2'   => 'ꪕ',
    'th1'  => 'ꪖ', 'th2'  => 'ꪗ',
    'n1'   => 'ꪘ', 'n2'   => 'ꪙ',
    'b1'   => 'ꪚ', 'b2'   => 'ꪛ',
    'p1'   => 'ꪜ', 'p2'   => 'ꪝ',
    'phh1' => 'ꪞ', 'phh2' => 'ꪟ',
    'ph1'   => 'ꪠ', 'ph2'   => 'ꪡ',
    'f1'   => 'ꪠ', 'f2'   => 'ꪡ',
    'm1'   => 'ꪢ', 'm2'   => 'ꪣ',
    'd1'   => 'ꪤ', 'd2'   => 'ꪥ',
    'r1'   => 'ꪦ', 'r2'   => 'ꪧ',
    'l1'   => 'ꪨ', 'l2'   => 'ꪩ',
    'v1'   => 'ꪪ', 'v2'   => 'ꪫ',
    'h1'   => 'ꪬ', 'h2'   => 'ꪭ',
    'o1'   => 'ꪮ', 'o2'   => 'ꪯ',
    ];
    $tPhuAmDau = strtr($pPhuAmDau_12, $mangAnhXaPhuAm);

    return $tPhuAmDau;
}
function tDauThanhDieu($word){
    $pPhuAmCuoi = pPhuAmCuoi($word);
    $HaiKyTuCuoi = mb_substr(pBoDauThanhDieu($word),-2,2);
    $pThanhDieu = pThanhDieu($word);
    //nếu phụ âm cuối tắc t, p, và ăc thì không dấu 
    //nếp pPhuAmCuoi -k cũng không dấu
    //còn lại có dấu 
    if($pPhuAmCuoi=='k'||$pPhuAmCuoi=='t'||$pPhuAmCuoi=='p'||$HaiKyTuCuoi=='ăc'){
        $tDauThanhDieu="";
    }else if($pThanhDieu==1||$pThanhDieu==4){
        $tDauThanhDieu="";
    }else if($pThanhDieu==2||$pThanhDieu==5){
        $tDauThanhDieu="꪿";
    }else if($pThanhDieu==3||$pThanhDieu==6){
        $tDauThanhDieu="꫁";
    }else{
        $tDauThanhDieu="";
    }
    return $tDauThanhDieu;
}
function tPhuAmSau($word) {
    $pPhuAmSau = pPhuAmSau($word);
    $ToThanhDieu = pToThanhDieu($word);
    $pPhuAmSau_12 = $pPhuAmSau . $ToThanhDieu;
    // Bảng ánh xạ phiên âm → chữ cái Thái Việt Nam
    $mangAnhXaPhuAm = [
    'k1'  => 'ꪀ', 'k2'   => 'ꪁ',
    'c1'  => 'ꪀ', 'c2'   => 'ꪁ',
    'q1'  => 'ꪀ', 'q2'   => 'ꪁ',
    'khh1' => 'ꪂ', 'khh2' => 'ꪃ',
    'kh1'  => 'ꪄ', 'kh2'  => 'ꪅ',
    'g1'   => 'ꪆ', 'g2'   => 'ꪇ',
    'ng1'  => 'ꪈ', 'ng2'  => 'ꪉ',
    'ngh1'  => 'ꪈ', 'ngh2'  => 'ꪉ',
    'ch1'  => 'ꪊ', 'ch2'  => 'ꪋ',
    'chh1' => 'ꪌ', 'chh2' => 'ꪍ',
    's1'   => 'ꪎ', 's2'   => 'ꪏ',
    'x1'   => 'ꪎ', 'x2'   => 'ꪏ',
    'nh1'  => 'ꪐ', 'nh2'  => 'ꪑ',
    'đ1'   => 'ꪒ', 'đ2'   => 'ꪓ',
    't1'   => 'ꪔ', 't2'   => 'ꪕ',
    'th1'  => 'ꪖ', 'th2'  => 'ꪗ',
    'n1'   => 'ꪘ', 'n2'   => 'ꪙ',
    'b1'   => 'ꪚ', 'b2'   => 'ꪛ',
    'p1'   => 'ꪜ', 'p2'   => 'ꪝ',
    'phh1' => 'ꪞ', 'phh2' => 'ꪟ',
    'ph1'   => 'ꪠ', 'ph2'   => 'ꪡ',
    'f1'   => 'ꪠ', 'f2'   => 'ꪡ',
    'm1'   => 'ꪢ', 'm2'   => 'ꪣ',
    'd1'   => 'ꪤ', 'd2'   => 'ꪥ',
    'r1'   => 'ꪦ', 'r2'   => 'ꪧ',
    'l1'   => 'ꪨ', 'l2'   => 'ꪩ',
    'v1'   => 'ꪪ', 'v2'   => 'ꪫ',
    'h1'   => 'ꪬ', 'h2'   => 'ꪭ',
    'o1'   => 'ꪮ', 'o2'   => 'ꪯ',
    ];
    if(!empty($pPhuAmSau)){
        $tPhuAmSau = strtr($pPhuAmSau_12, $mangAnhXaPhuAm);
    }else{
        $tPhuAmSau="";
    }
    

    return $tPhuAmSau;
}
function tBanAmVo($word){
    $pBoDau=pBoDauThanhDieu($word);
    // Danh sách tổ hợp có bán âm o u
    $mangOU = ['oa', 'oă', 'oe', 'uă', 'uê', 'uy', 'uơ'];

    // Kiểm tra xem từ có chứa tổ hợp nào thuộc mảng bán âm o u không
    foreach ($mangOU as $item) {
        if (mb_strpos($pBoDau, $item) !== false) {
            return 'ꪫ';
        }
    }

    // Nếu không tìm thấy thì trả về rỗng
    return '';
}
function tNguyenAmTruoc($word){
    $pNguyenAm = pNguyenAm($word);
    $mangWA = ['ê', 'ơ', 'â', 'ưa', 'ươ', 'au', 'âu'];
    $mangTruoc = ['e', 'ô', 'aư', 'ăư', 'ay', 'ây'];
    $AnhXaNguyenAm = ['e' => 'ꪵ', 'ô' => 'ꪶ', 'aư' => 'ꪻ', 'ăư' => 'ꪻ', 'ay' => 'ꪼ', 'ây' => 'ꪼ'];
    //Tìm may kưa, nguyên âm có may cưa: ê, ơ, ưa, ươ, au, âu
    //Tìm may ke, kô, kaư, kay
    if(in_array($pNguyenAm, $mangWA)){
        $tNguyenAmTruoc="ꪹ";
    }else if(in_array($pNguyenAm, $mangTruoc)){
        $tNguyenAmTruoc=strtr($pNguyenAm, $AnhXaNguyenAm);
    }else{
        $tNguyenAmTruoc="";
    }
    
    return $tNguyenAmTruoc;
}
function tNguyenAmSau($word){
    //Là các may gõ sau phụ âm đầu/bán âm: trên, sau, dưới
    $pNguyenAm = pNguyenAm($word);
    $pPhuAmCuoi = pPhuAmCuoi($word);
    $mangtNguyenAmSau = ['ă', 'a', 'i', 'y', 'ư', 'u', 'ia', 'iê', 'yê', 'ua', 'uô', 'uâ', 'ê', 'ơ', 'â', 'ăn', 'ăm', 'au','âu'];
    $AnhXatNguyenAmSau = ['ăn' => 'ꪽ', 'ăm' => 'ꪾ', 'ă' => 'ꪰ', 'a' => 'ꪱ', 'i' => 'ꪲ', 'y' => 'ꪲ', 'ư' => 'ꪳ', 'u' => 'ꪴ', 'ia' => 'ꪸ', 'iê' => 'ꪸ', 'yê' => 'ꪸ', 'ua' => 'ꪺ', 'uô' => 'ꪺ', 'uâ' => 'ꪺ', 'ê' => 'ꪸ', 'ơ' => 'ꪷ', 'â' => 'ꪷ', 'au' => 'ꪱ' ,'âu' => 'ꪱ'];
    //Nếu nguyên âm là o thì kiểm tra phụ âm cuối 
    //Ánh xạ nguyên âm sau có trong và ứng với pNguyenAmSau
    if($pNguyenAm=='o'){
        if(empty($pPhuAmCuoi)){
            $tNguyenAmSau="ꪷ";
        }else{
            $tNguyenAmSau="ꪮ";
        }
    }else if(in_array($pNguyenAm, $mangtNguyenAmSau)){
        $tNguyenAmSau=strtr($pNguyenAm, $AnhXatNguyenAmSau);
    }else{
        $tNguyenAmSau="";
    }
    
    return $tNguyenAmSau;
}
function tPhuAmCuoiCam($word){
    $mangtPhuAmCuoiCam=['oe', 'uê', 'uy', 'uy', 'ue', 'uê', 'uơ'];
    $pBoDau = pBoDauThanhDieu($word);
    $HaiKyTuCuoi = mb_substr($pBoDau,-2,2);
    
    if($pBoDau=="qui"||in_array($HaiKyTuCuoi, $mangtPhuAmCuoiCam)){
        return 'ꪥ';
    }
    return '';
}
function thaytheQuGi($word){
    $quae = mb_substr(pBoDauThanhDieu($word),0,3);
    $mangQui = ['qui' => 'kuy', 'quì' => 'kuỳ', 'quí' => 'kuý', 'quỉ' => 'kuỷ', 'quĩ' => 'kuỹ', 'quị' => 'kuỵ'];
    $mangGi = ['gì' => 'dì', 'gí' => 'dí', 'gỉ' => 'dỉ', 'gĩ' => 'dĩ', 'gị' => 'dị'];
    $word = str_replace('gi','d',$word);
    $word = $word = strtr($word, $mangQui);
    if($quae=='qua'||$quae=='que'){
        $word = str_replace('qu','ko',$word);
    }else if($quae=='qui'){
        $word = strtr($word, $mangQui);
    }else{
        $word = str_replace('qu','ku',$word);
    }
    return $word;
}
function tPhienAmTuNgu($word){
    // Nếu từ toàn là chữ Thái thì không phiên âm nữa
    if (LaChuThai($word)) {
        return $word;
    }
    /*Nếu từ là * (thành ngữ, CDTN) thì không phiên âm nữa: áp dụng ở hàm phiên âm văn bản
    if ($word=="*") {
        return $word;
    }
    */

    $word = mb_strtolower($word);
    $word = thaytheQuGi($word);
    
    // Loại bỏ ký tự đặc biệt và chữ số (chỉ giữ lại chữ cái và dấu thanh)
    $word = preg_replace('/[^\p{L}\p{Mn}]/u', '', $word);
    // \p{L} = chữ cái, \p{Mn} = dấu thanh tổ hợp Unicode (như ◌̀ ◌́...)

    // Nếu sau khi làm sạch, từ rỗng thì trả lại rỗng luôn
    if ($word === '') return '';
    
    //Hàm: Từ ngữ phiên âm sang chữ thái/ Chuyển 1 từ phiên âm sang 1 từ chữ Thái
    //Nếu nguyên âm ơ, ê nối kiểu khác
    
    $pNguyenAm = pNguyenAm($word);
    $tNguyenAmTruoc = tNguyenAmTruoc($word);
    $tPhuAmDau = tPhuAmDau($word);
    $tPhuAmSau = tPhuAmSau($word);
    $tDauThanhDieu = tDauThanhDieu($word);
    $tNguyenAmSau = tNguyenAmSau($word);
    $tBanAmVo = tBanAmVo($word);
    $tPhuAmCuoi = tPhuAmCuoi($word);
    $tPhuAmCuoiCam = tPhuAmCuoiCam($word);
    if($pNguyenAm=='ê'||$pNguyenAm=='ơ'){
        if(empty($tPhuAmSau)){
            $tPhienAmTuNgu = $tNguyenAmTruoc.$tPhuAmDau.$tNguyenAmSau.$tDauThanhDieu.$tBanAmVo.$tPhuAmCuoi.$tPhuAmCuoiCam;
        }else{
            $tPhienAmTuNgu = $tNguyenAmTruoc.$tPhuAmDau.$tNguyenAmSau.$tPhuAmSau.$tDauThanhDieu.$tBanAmVo.$tPhuAmCuoi.$tPhuAmCuoiCam;
        }
        
    }else if(($pNguyenAm=='a'||$pNguyenAm=='ăn'||$pNguyenAm=='ua'||$pNguyenAm=='uô'||$pNguyenAm=='o')&&$tNguyenAmSau!=="ꪷ"){
        $tPhienAmTuNgu = $tNguyenAmTruoc.$tPhuAmDau.$tPhuAmSau.$tDauThanhDieu.$tBanAmVo.$tNguyenAmSau.$tPhuAmCuoi.$tPhuAmCuoiCam;
    }else if($tNguyenAmTruoc=='ꪹ'&&$tNguyenAmSau=="ꪱ"){
        $tPhienAmTuNgu = $tNguyenAmTruoc.$tPhuAmDau.$tPhuAmSau.$tDauThanhDieu.$tNguyenAmSau;
    }else{
        $tPhienAmTuNgu = $tNguyenAmTruoc.$tPhuAmDau.$tPhuAmSau.$tBanAmVo.$tNguyenAmSau.$tDauThanhDieu.$tPhuAmCuoi.$tPhuAmCuoiCam;
    }
    return $tPhienAmTuNgu;
}
function PhienAmSangChuThai($VanBan) {
    $VanBan = DauNgangSangDauNga_PAsCT($VanBan);
    $VanBan = thayTheAw(mb_strtolower($VanBan));

    // Giữ lại xuống dòng -> tách văn bản thành các dòng
    $dongArray = preg_split('/\R/u', $VanBan); // \R = bất kỳ kiểu xuống dòng

    $ketQuaDong = [];

    foreach ($dongArray as $dong) {
        // Tách từng dòng thành từ
        $tuNguArray = preg_split('/\s+/u', trim($dong));
        $ketQua = [];

        foreach ($tuNguArray as $tuNgu) {
            if ($tuNgu === '') continue;

            if (mb_substr($tuNgu, 0, 1) === '*') {
                $ketQua[] = $tuNgu;
                continue;
            }

            if (preg_match('/^[.,!?;:"()\[\]{}<>\-\d]+$/u', $tuNgu)) {
                $ketQua[] = $tuNgu;
                continue;
            }

            $parts = preg_split('/([.,!?;:"()\[\]{}<>\-])/', $tuNgu, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $phienAmParts = [];
            foreach ($parts as $part) {
                if (preg_match('/^[.,!?;:"()\[\]{}<>\-]$/u', $part)) {
                    $phienAmParts[] = $part;
                } else {
                    $phienAmParts[] = tPhienAmTuNgu($part);
                }
            }

            $ketQua[] = implode('', $phienAmParts);
        }

        // Ghép lại thành một dòng
        $ketQuaDong[] = implode(' ', $ketQua);
    }

    // Ghép các dòng lại với nhau, giữ nguyên xuống dòng
    return implode("\n", $ketQuaDong);
}
?>