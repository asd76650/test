<?php

//로그인 상태일 때, 세션 파괴 후 목록으로 이동
if(isset($_SESSION['id']) && !empty($_SESSION['id'])){
    session_destroy();
    echo "<script>location.href = 'list.php'</script>";
}else{
    //아이디와 패스워드 값이 존재할 때
    if(!empty($_POST['id']) && !empty($_POST['password'])){
        //db 연결
        $connect = mysql_connect("localhost", "root", "autoset");
        $dbConn = mysql_select_db("test");

        $q = "select * from member where member_id = '".$_POST['id']."'";
        $row = mysql_fetch_array(mysql_query($q));

        //id에 해당하는 password 가 존재
        if($_POST['password'] == $row['member_pw']){
            session_start();
            $_SESSION['id'] = $row['member_id'];
            $_SESSION['nickName'] = $row['member_nick'];
            $_SESSION['name'] = $row['member_name'];
            $_SESSION['password'] = $row['member_pw'];
            echo "<script>location.href='list.php'</script>";
        }else
            echo "<script>alert('비밀번호를 다시 입력하세요.');location.href='list.php';</script>";
    }else{
        echo "<script>alert('아이디 또는 비밀번호를 입력하세요');</script>";
        echo "<script>location.href='list.php'</script>";
    }
}




?>