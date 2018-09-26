window.onload = function()
{
    document.getElementById("delete_btn").addEventListener("click", function(event){
        event.preventDefault()
    });

    var btn = document.getElementById('delete_btn');
    console.log(btn);

    btn.onclick = function() { 
        var r = confirm("do you want to delete this email");
        if (r == true) {
            document.forms["delete_form"].submit();
        }
    };
};