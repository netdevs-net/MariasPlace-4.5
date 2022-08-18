// jQuery(document).ready(function () {
//     jQuery(document).on('click', 'div[id^=team-]', function(){
//         var curentimg = jQuery(this).find("div").children("img");
//         var curentposition = jQuery(this).find("span").text();
//         var curentname = jQuery(this).find("h3").html();
//         var curentid = jQuery(this).attr("id");
//         var tid = curentid.split("-")[1]; 
//         var templatedir = jQuery(this).find("div").text();
//         jQuery.ajax({
//             type: 'POST',
//             url: '/wp-admin/admin-ajax.php',
//             data: {
//                 'post_id': tid,
//                 'action': 'getPostcontentteam' //this is the name of the AJAX method called in WordPress
//             }, success: function (data) {
//                 console.log(data);
//                // jQuery('#exampleModalPreview .team_member_details .bio-inner h1').text(curentname);
//                // jQuery('#exampleModalPreview .team_member_details .bio-inner .title').text(curentposition);
//                // jQuery('#exampleModalPreview .team_member_details .bio-inner .team-desc').text(result);
//             },
//             error: function () {
//                 alert("error");
//             }
//         });
//         // jQuery.ajax({ 
//         // url: '/wp-content/themes/MariasPlace/inc/addons/teamajax.php',
//         // data: {"teamID": tid},
//         // type: 'post',
//         // success: function(result)
//         // {   console.log(result);
//         //     jQuery('.modal-box').text(result).fadeIn(700, function() 
//         //     {  
//         //         setTimeout(function() 
//         //         {
//         //             jQuery('.modal-box').fadeOut();
//         //         }, 2000);
//         //     });
//         // }
//         // });
//     }); 
// });
