$(document).ready(function () {
    // check admin password is correct or not

    $("#current_password").keyup(function () {
        var current_password = $("#current_password").val();
        alert(current_password);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: '/admin/check-current-password',
            data: { current_password: current_password },
            success: function (resp) {
                if (resp == "false") {
                    $("#verifyCurrentPwd").html("Current Password is Incorrect");
                } else if (resp == "true") {
                    $("#verifyCurrentPwd").html("Current Password is Correct");
                }
            }, error: function () {
                alert("error");
            }
        })
    });
    // Update CMS Page Status
    $(document).on("click", ".updateCmsPageStatus", function () {
        var status = $(this).children("i").attr("status");
        var page_id = $(this).attr("page_id");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: '/admin/update-cms-page-status',
            data: { status: status, page_id: page_id },
            success: function (resp) {
                if (resp['status'] == 0) {
                    $("#page-" + page_id).html("<i class='fas fa-toggle-off' style='color:gray' status='Inactive'></i>");
                } else if (resp['status'] == 1) {
                    $("#page-" + page_id).html("<i class='fas fa-toggle-on' style='color:blue' status='Active'></i>");
                }


            }, error: function () {
                alert("error");
            }

        })
    });

    // Confrim of deletion of CMS page
    /*$(document).on("click",".confirmDelete",function(){

       var name = $(this).attr('name');
       if(confirm('Are you sure to delete this'+name+'?')){
        return true;
       }
       return false;
    });*/
    // Confrim Deletion with SweetAlert

    $(document).on("click", ".confirmDelete", function () {
        var record = $(this).attr('record');
        var recordid = $(this).attr('recordid');
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                });
                window.location.href = "/admin/delete-"+record+"/"+recordid;
            }
        });
    });

});