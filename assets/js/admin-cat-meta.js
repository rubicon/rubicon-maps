jQuery(document).ready(function ($) {
  $(".rubicon-upload").click(function (e) {
    e.preventDefault();
    let target = $(this).data("target");
    let custom_uploader = wp
      .media({
        title: "Select Icon",
        button: { text: "Use this image" },
        multiple: false,
      })
      .on("select", function () {
        let attachment = custom_uploader.state().get("selection").first().toJSON();
        $("#" + target).val(attachment.id);
        $("#" + target + "-preview")
          .attr("src", attachment.url)
          .show();
      })
      .open();
  });
});
