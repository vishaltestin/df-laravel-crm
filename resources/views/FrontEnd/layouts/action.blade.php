
<script>
    function del_rec(this_script)
    {
        formObj=document.form;
        var flag=0;
        for (var i=0;i < formObj.length;i++)
        {
            fldObj = formObj.elements[i];
            if (fldObj.type == 'checkbox')
            {
                if(fldObj.checked)
                {
                    flag=1;
                }
            }
        }
        if(flag==0)
        {
            alert("Please select at least one record to delete!");

        }
        else
        {
            if(confirm("Are you sure you want to delete record!"))
            {
                var formobj=document.form;
                formobj.action= this_script + "/Delete";
                formobj.submit();
            }
        }

    }


    <!-- Begin
    function Check(chk)
    {

        if(document.form.allchk.checked==true){
            for (i = 0; i < chk.length; i++)
                chk[i].checked = true ;
        }else{

            for (i = 0; i < chk.length; i++)
                chk[i].checked = false ;
        }
    }
    // End -->



    function act_rec(this_script)
    {
        formObj=document.form;
        var flag=0;
        for (var i=0;i < formObj.length;i++)
        {
            fldObj = formObj.elements[i];
            if (fldObj.type == 'checkbox')
            {
                if(fldObj.checked)
                {
                    flag=1;
                }
            }
        }
        if(flag==0)
        {
            alert("Please select at least one record to Activate!");

        }
        else
        {
            if(confirm("Are you sure you want to activate record!"))
            {
                var formobj=document.form;
                formobj.action=this_script + "/Active";
                formobj.submit();
            }
        }

    }


    function inact_rec(this_script)
    {
        formObj=document.form;
        var flag=0;
        for (var i=0;i < formObj.length;i++)
        {
            fldObj = formObj.elements[i];
            if (fldObj.type == 'checkbox')
            {
                if(fldObj.checked)
                {
                    flag=1;
                }
            }
        }
        if(flag==0)
        {
            alert("Please select at least one record to Inactivate!");

        }
        else
        {
            if(confirm("Are you sure you want to Inactivate record!"))
            {
                var formobj=document.form;
                formobj.action=this_script + "/Inactive";
                formobj.submit();
            }
        }

    }

    function inact_rej(this_script)
    {
        formObj=document.form;
        var flag=0;
        for (var i=0;i < formObj.length;i++)
        {
            fldObj = formObj.elements[i];
            if (fldObj.type == 'checkbox')
            {
                if(fldObj.checked)
                {
                    flag=1;
                }
            }
        }
        if(flag==0)
        {
            alert("Please select at least one record to Inactivate!");

        }
        else
        {
            if(confirm("Are you sure you want to Reject record!"))
            {
                var formobj=document.form;
                formobj.action=this_script + "/Reject";
                formobj.submit();
            }
        }

    }



</script>

