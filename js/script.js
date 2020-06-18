function CallPrint(id) {

    let prtContent = document.getElementById(id);
    let prtCSS = '<style> table {border: 1px solid #ccc; padding-left: 5px; border-collapse: collapse;} td {border: 1px solid #ccc; padding-left: 5px; border-collapse: collapse;} a { text-decoration: none; color: black} </style>';
    let NewWindow = window.open('','','left=50,top=50,width=800,height=640,toolbar=0,scrollbars=1,status=0');

    NewWindow.document.write(prtCSS);
    NewWindow.document.write(prtContent.innerHTML);

}