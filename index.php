<?php
require("DeliveryMethods.php");
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <title>Delivary Methods</title>
    <style>
        .options, .ranges, .show_options, .add_ranges{
            display:none;
        }
        .panel:hover .show_options,.panel:hover .add_ranges {
            display:inline;
        }
        .panel-body.ranges{
            background-color:#29edf7
        }
        .panel-body.options{
            background-color:#C9edf7
        }
        label{
            line-height: 30px;
        }
        .error{
            color:red;
            text-align:center
        }
    </style>

    <script>
        $(document).ready(function(){
            $(".show_ranges").click(function(){
                id = $(this).attr("id").split("show_ranges-");
                if($("#range-" + id[1]).css("display") == "block") $("#range-" + id[1]).css("display","none");
                else $("#range-" + id[1]).css("display","block");

                $("#option-" + id[1]).css("display","none");
            });

            $(".add_ranges").click(function(){
                id = $(this).attr("id").split("add_ranges-");

                if($("#new_ranges-"+id[1])){

                    $("#new_ranges-"+id[1]).html($(".range-template-"+id[1]).html());

                    $("#new_ranges-"+id[1]).find(".range_from-new").attr("id", "range_from-" + id[1] + "-0");
                    $("#new_ranges-"+id[1]).find(".range_to-new").attr("id", "range_to-" + id[1] +  "-0");
                    $("#new_ranges-"+id[1]).find(".range_price-new").attr("id", "range_price-" + id[1] + "-0");

                    if($("#new_ranges-" + id[1]).css("display") == "block") $("#new_ranges-" + id[1]).css("display","none");
                    else $("#new_ranges-" + id[1]).css("display","block");
                }
                else{
                    if($("#range-" + id[1]).css("display") == "block") $("#range-" + id[1]).css("display","none");
                    else $("#range-" + id[1]).css("display","block");
                }
               
                $("#option-" + id[1]).css("display","none");
            });

            $(".show_options").click(function(){
                id = $(this).attr("id").split("show_options-");
                if($("#option-" + id[1]).css("display") == "block") $("#option-" + id[1]).css("display","none");
                else $("#option-" + id[1]).css("display","block");

                $("#range-" + id[1]).css("display","none");
            });

            $('body').on('click', '.add_new_range', function(){
                id = $(this).attr("id").split("add_new_range-");
                $(this).parent().after($(".range-template-"+id[1]).html());

                var order_row = 0;
                $(this).parent().parent().find(".row").each(function(){
                    $(this).find(".range_from-new").attr("id", "range_from-" + id[1] + "-" + order_row + "-" +id[1]);
                    $(this).find(".range_to-new").attr("id", "range_to-" + id[1] + "-" + order_row + "-" + id[1]);
                    $(this).find(".range_price-new").attr("id", "range_price-" + id[1] + "-"+ order_row + "-" + id[1]);
                    order_row++;
                });
            });

            $('body').on('click', '.delete_range', function(){
                $(this).parent().remove();
            });

            $("#save_form").click(function(){
                $.ajax({
                    url: 'index.php',
                    type: 'post',
                    dataType: 'json',
                    data: $('form').serialize(),
                    success: function(data) {
                        if(data.success) alert("Data is succesfully saved")
                        else if(data.error) alert(data.error)
                        else if(data.error_form){
                            $(".error").remove();
                            for(key in data.error_form) {
                                for(delivery_method_id in data.error_form[key] )
                                $("#"+key+"-"+delivery_method_id).append("<span class='error col-md-12'>" +  data.error_form[key][delivery_method_id]+"</span>");
                            }
                        }
                    }
                });
            });

        })
    </script>
</head>
<body>
    <h2 class="col-md-12">Delivary methods</h2>

    <div class="container container-fluid col-md-12">
    <form>
    <?php foreach($deliveryMethods as $id => $deliveryMethod){ ?>

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="col-md-5 panel-title"><?php echo $deliveryMethod["name"]?></h3>

                    <?php if($deliveryMethod["price"] == 0){?>

                        <h3 class="col-md-1 panel-title">Free</h3>

                    <?php } else if ($deliveryMethod["price"] > 0) { ?>
                        <span class="col-md-2" id="price-<?php echo $id ?>"><input type="text"  name="price[<?php echo $id ?>]" value="<?php echo $deliveryMethod["price"]?>">$</span>
                    <?php } ?>

                    <?php if(count($deliveryMethod["ranges"]) > 0){ ?>

                        <a href="#" class="show_ranges" id="show_ranges-<?php echo $id?>">Show ranges</a>

                   <?php }else{ ?>

                        <a href="#" class="add_ranges" id="add_ranges-<?php echo $id?>">Add Ranges</a>

                    <?php } ?>


                     <a href="#" class="show_options" style="float:right" id="show_options-<?php echo $id?>">Show Options</a>

                     <div class="clearfix"></div>

                    </div>
                    <?php if(count($deliveryMethod["ranges"]) > 0){ ?>
                        <div class="panel-body ranges" id="range-<?php echo $id?>">
                            <?php foreach($deliveryMethod["ranges"] as $range){ ?>
                                <div class="row">
                                    <span class="col-md-2" id="range_from-<?php echo $range['id'] . "-" . $id ?>" >
                                        <label class="col-md-3">From</label><input type="text" class="col-md-7" name="range_from[<?php echo $id ?>][][<?php echo $range['id']?>]" value="<?php echo $range['from'] ?>">$
                                    </span>
                                    <span class="col-md-2" id="range_to-<?php echo $range['id'] . "-" . $id  ?>">
                                        <label class="col-md-3">To</label><input type="text" class="col-md-7"  name="range_to[<?php echo $id ?>][][<?php echo $range['id']?>]" value="<?php echo $range['to'] ?>">$
                                    </span>
                                    <span class="col-md-2" id="range_price-<?php echo $range['id'] . "-" . $id ?>">
                                        <input type="text" class="col-md-offset-1" name="range_price[<?php echo $id ?>][][<?php echo $range['id']?>]" value="<?php echo $range['price'] ?>">$
                                    </span>
                                    <a href="#" class="col-md-offset-3 add_new_range" id="add_new_range-<?php echo $id ?>">Add New</a>
                                    <a href="#" class="col-md-offset-1 delete_range">Delete</a>
                                </div>
                            <?php } ?>
                        </div>

                    <?php } else { ?>

                        <div class="panel-body ranges" id="new_ranges-<?php echo $id?>"></div>

                    <?php } ?>

                        <div class="panel-body options" id="option-<?php echo $id?>" >

                                <div class="row">
                                    <span class="col-md-5"> <label>Delivary Url</label></span>
                                    <span class="col-md-5" id="delivery_url-<?php echo $id ?>">
                                        <input type="text"  name="delivery_url[<?php echo $id ?>]" value="<?php echo $deliveryMethod['delivery_url']?>">
                                    </span>
                                </div>
                                <div class="row">&nbsp;</div>
                                <div class="row">
                                    <span class="col-md-5"> <label>Weight (accpeted deliveries in KG) Url</label></span>
                                    <span  class="col-md-7">
                                         <span class="col-md-4" id="from_weight-<?php echo $id ?>">From <input type="text" name="from_weight[<?php echo $id ?>]" value="<?php echo $deliveryMethod['from_weight']?>"></span>
                                         <span class="col-md-4" id="to_weight-<?php echo $id ?>">To <input type="text" name="to_weight[<?php echo $id ?>]" value="<?php echo $deliveryMethod['to_weight']?>">KG</span>
                                    </span>
                                </div>
                                <div class="row">&nbsp;</div>
                                <div class="row">
                                   <span class="col-md-5"><label>Notes</label></span>
                                   <span class="col-md-5"><textarea  name="notes[<?php echo $id ?>]"><?php echo $deliveryMethod['notes']?></textarea></span>
                                </div>
                         </div>

                </div>
        
        
        <?php } ?>
        <input type="hidden" name="save" value="save">
        </form>
         <?php foreach($deliveryMethods as $id => $deliveryMethod){ ?>
         <div class="range-template-<?php echo $id ?>"  style="display:none">
            <div class="row">
                <span class="col-md-2 range_from-new">
                    <label class="col-md-3">From </label><input type="text" class="col-md-7" name="range_from[<?php echo $id ?>][][-1]" value="">$
                </span>
                <span class="col-md-2 range_to-new">
                    <label class="col-md-3">To</label><input type="text" class="col-md-7" name="range_to[<?php echo $id ?>][][-1]" value="">$
                </span>
                <span class="col-md-2 range_price-new">
                    <input type="text" class="col-md-offset-1" name="range_price[<?php echo $id ?>][][-1]" value="">$
                </span>
                <a href="#" class="col-md-offset-3 add_new_range" id="add_new_range-<?php echo $id ?>">Add New</a>
                <a href="#" class="col-md-offset-1 delete_range">Delete</a>
            </div>
        </div>
        <?php } ?>
        <div class="pull-right">
            <button class="btn" id="save_form">Save Form</button>
        </div>
    </div>

   

</body>
</html>
