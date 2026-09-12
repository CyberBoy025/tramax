import $ from "jquery";
window.$ = window.jQuery = $;

import "bootstrap";

// Any <form data-confirm="..."> asks for a native confirm() before
// submitting — used on every destructive (delete) action across the admin
// dashboard, replacing the confirmation dialogs Phase 6+ pages relied on
// in the previous React frontend.
$(function () {
  $(document).on("submit", "form[data-confirm]", function (event) {
    const message = $(this).data("confirm");
    if (!window.confirm(message)) {
      event.preventDefault();
    }
  });
});

// Ports components/ui/image-upload-field.tsx: pick a file, upload it
// immediately via AJAX, write the returned URL into a hidden input so the
// surrounding <form>'s normal POST/PATCH submit picks it up unchanged.
// Every .image-upload block (resources/views/admin/partials/image-upload.blade.php)
// wires itself up the same way, regardless of which page it's on.
$(function () {
  $(document).on("change", ".image-upload__file", function () {
    const $wrap = $(this).closest(".image-upload");
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("file", file);
    formData.append("context", $wrap.data("context"));

    $wrap.find(".image-upload__status").text("Uploading…").removeClass("text-danger");

    $.ajax({
      url: $wrap.data("upload-url"),
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
    })
      .done(function (res) {
        $wrap.find(".image-upload__hidden").val(res.data.url);
        $wrap.find(".image-upload__preview").attr("src", res.data.url).removeClass("d-none");
        $wrap.find(".image-upload__status").text("");
      })
      .fail(function (xhr) {
        const message = xhr.responseJSON?.message || "Upload failed.";
        $wrap.find(".image-upload__status").text(message).addClass("text-danger");
      });
  });
});

// Repeatable row groups (currently: royalty statement line items). Any
// .repeatable-rows container with a <template> holding one row and a
// [data-add-row] button gets "add another row" / "remove this row" wired
// up generically — the __INDEX__ placeholder in the template's input names
// is replaced with an incrementing counter so Laravel receives a proper
// line_items[0][source]-style array regardless of how many rows exist.
$(function () {
  $(document).on("click", "[data-add-row]", function () {
    const $container = $(this).closest(".repeatable-rows");
    const template = $container.find("template").html();
    const index = Number($container.data("next-index") || 0);
    $container.data("next-index", index + 1);
    $container.find(".repeatable-rows__items").append(template.replaceAll("__INDEX__", index));
  });

  $(document).on("click", "[data-remove-row]", function () {
    $(this).closest(".repeatable-rows__row").remove();
  });
});
