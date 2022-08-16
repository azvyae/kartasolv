function createDatatable(config) {
  return $(`#${config.table_id}`).DataTable(loadDatatableConfig(config));
}

function loadDatatableConfig(config) {
  var Criteria = $.fn.dataTable.Criteria;
  var buttons;
  config.columns.map(function (e) {
    if (config.ajax.data.orderable.indexOf(e.name) != -1) {
      Object.assign(e, {
        orderable: true,
      });
    } else {
      Object.assign(e, {
        orderable: false,
      });
    }
  });
  return (
    (buttons = [
      { extend: "pageLength", attr: { title: "Ubah Jumlah data tampil" }, dom: { buttonLiner: null } },
      {
        attr: { title: "Filter Data" },
        extend: "searchBuilder",
        config: {
          depthLimit: 1,
          columns: config.ajax.data.searchable.map(function (e) {
            return e + ":name";
          }),
          conditions: {
            string: {
              "!=": null,
              starts: null,
              "!starts": null,
              "!contains": null,
              ends: null,
              "!ends": null,
              "!null": null,
              null: null,
            },
            num: { null: null, "!=": null, "!between": null, "!null": null, between: null },
            date: { null: null, "!=": null, "!between": null, "!null": null, between: null },
            html: { null: null, "=": null, "!=": null, starts: null, "!starts": null, "!contains": null, ends: null, "!ends": null, "!null": null },
            array: {
              "=": {
                init: function (that, fn, preDefined = null, array = false) {
                  let column = that.dom.data.children("option:selected").val();
                  let indexArray = that.s.dt.rows().indexes().toArray();
                  let settings = that.s.dt.settings()[0];
                  that.dom.valueTitle.prop("selected", true);

                  // Declare select element to be used with all of the default classes and listeners.
                  let el = $("<select/>")
                    .addClass(Criteria.classes.value)
                    .addClass(Criteria.classes.dropDown)
                    .addClass(Criteria.classes.italic)
                    .addClass(Criteria.classes.select)
                    .append(that.dom.valueTitle)
                    .on("change.dtsb", function () {
                      $(this).removeClass(Criteria.classes.italic);
                      fn(that, this);
                    });

                  if (that.c.greyscale) {
                    el.addClass(Criteria.classes.greyscale);
                  }

                  let added = [];
                  let options = [];

                  // Function to add an option to the select element
                  let addOption = (filt, text) => {
                    if (that.s.type.includes("html") && filt !== null && typeof filt === "string") {
                      filt.replace(/(<([^>]+)>)/gi, "");
                    }

                    // Add text and value, stripping out any html if that is the column type
                    let opt = $("<option>", {
                      type: Array.isArray(filt) ? "Array" : "String",
                      value: filt,
                    })
                      .data("sbv", filt)
                      .addClass(that.classes.option)
                      .addClass(that.classes.notItalic)
                      // Have to add the text this way so that special html characters are not escaped - &amp; etc.
                      .html(typeof text === "string" ? limitText(text.replace(/(<([^>]+)>)/gi, "")) : limitText(text));

                    let val = opt.val();

                    // Check that this value has not already been added
                    if (added.indexOf(val) === -1) {
                      added.push(val);
                      options.push(opt);

                      if (preDefined !== null && Array.isArray(preDefined[0])) {
                        preDefined[0] = preDefined[0].sort().join(",");
                      }

                      // If this value was previously selected as indicated by preDefined, then select it again
                      if (preDefined !== null && opt.val() === preDefined[0]) {
                        opt.prop("selected", true);
                        el.removeClass(Criteria.classes.italic);
                        that.dom.valueTitle.removeProp("selected");
                      }
                    }
                  };

                  // Add all of the options from the table to the select element.
                  // Only add one option for each possible value

                  if (config.columns[column].options) {
                    config.columns[column].options.forEach(function (val) {
                      addOption(val.value, val.text);
                    });
                  } else {
                    for (let index of indexArray) {
                      let filter = settings.oApi._fnGetCellData(settings, index, column, typeof that.c.orthogonal === "string" ? that.c.orthogonal : that.c.orthogonal.search);
                      let value = {
                        filter:
                          typeof filter === "string"
                            ? filter.replace(/[\r\n\u2028]/g, " ") // Need to replace certain characters to match search values
                            : filter,
                        index,
                        text: settings.oApi._fnGetCellData(settings, index, column, typeof that.c.orthogonal === "string" ? that.c.orthogonal : that.c.orthogonal.display),
                      };

                      // If we are dealing with an array type, either make sure we are working with arrays, or sort them
                      if (that.s.type === "array") {
                        value.filter = !Array.isArray(value.filter) ? [value.filter] : value.filter;
                        value.text = !Array.isArray(value.text) ? [value.text] : value.text;
                      }

                      // If this is to add the individual values within the array we need to loop over the array
                      if (array) {
                        for (let i = 0; i < value.filter.length; i++) {
                          addOption(value.filter[i], value.text[i]);
                        }
                      }
                      // Otherwise the value that is in the cell is to be added
                      else {
                        addOption(value.filter, Array.isArray(value.text) ? value.text.join(", ") : value.text);
                      }
                    }
                  }

                  /*****************************************************************************************/
                  // addOption("PersistentNama", "PersistentNama");
                  /*****************************************************************************************/

                  options.sort((a, b) => {
                    if (that.s.type === "array" || that.s.type === "string" || that.s.type === "html") {
                      if (a.val() < b.val()) {
                        return -1;
                      } else if (a.val() > b.val()) {
                        return 1;
                      } else {
                        return 0;
                      }
                    } else if (that.s.type === "num" || that.s.type === "html-num") {
                      if (+a.val().replace(/(<([^>]+)>)/gi, "") < +b.val().replace(/(<([^>]+)>)/gi, "")) {
                        return -1;
                      } else if (+a.val().replace(/(<([^>]+)>)/gi, "") > +b.val().replace(/(<([^>]+)>)/gi, "")) {
                        return 1;
                      } else {
                        return 0;
                      }
                    } else if (that.s.type === "num-fmt" || that.s.type === "html-num-fmt") {
                      if (+a.val().replace(/[^0-9.]/g, "") < +b.val().replace(/[^0-9.]/g, "")) {
                        return -1;
                      } else if (+a.val().replace(/[^0-9.]/g, "") > +b.val().replace(/[^0-9.]/g, "")) {
                        return 1;
                      } else {
                        return 0;
                      }
                    }
                  });

                  for (let opt of options) {
                    el.append(opt);
                  }

                  return el;
                },
              },
              "!=": null,
              starts: null,
              "!starts": null,
              "!contains": null,
              contains: null,
              without: null,
              ends: null,
              "!ends": null,
              "!null": null,
              null: null,
            },
          },
        },
      },
    ]),
    config.buttons.hasOwnProperty("xlsx") &&
      buttons.push({
        extend: "excelHtml5",
        text: '<span class="bi bi-file-earmark-spreadsheet-fill"></span>',
        attr: { title: "Export dalam format Excel" },
      }),
    config.buttons.hasOwnProperty("add") &&
      buttons.push({
        text: '<i class="bi bi-plus-circle"></i>',
        attr: { title: "Tambah data" },
        action: function () {
          window.location.href = config.buttons.add.url;
        },
      }),
    config.buttons.hasOwnProperty("delete") &&
      $(document.body).append(
        `<div class="modal fade" id="delete_modal" tabindex="-1" aria-labelledby="delete_modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="delete_modalLabel">Konfirmasi Penghapusan Data</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Batalkan"></button>
            </div>
            <div class="modal-body">
              Apakah anda yakin ingin menghapus <span id="delete-records-number"></span> data?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
              <button type="button" id="confirm-delete-records" class="btn btn-danger">Hapus</button>
            </div>
          </div>
        </div>
      </div>`
      ) &&
      $("#confirm-delete-records").on("click", function () {
        $.ajax({
          type: "POST",
          url: config.buttons.delete.url,
          data: window.deletePostData,
          success: function (response) {
            response > 0 && location.reload();
          },
          error: function () {
            // location.reload();
          },
        });
      }) &&
      buttons.push({
        text: '<i class="bi bi-trash3"></i>',
        attr: { type: "button", title: "Hapus data" },
        action: function () {
          (postData = { selections: mapSelection(this.rows({ selected: !0 })[0], this.ajax.json().ids), _method: "DELETE" }),
            Object.assign(postData, config.buttons.delete.postData),
            postData.selections.length > 0 &&
              ($("#delete-records-number").html(postData.selections.length), new bootstrap.Modal("#delete_modal", {}).show(), (window.deletePostData = postData)),
            document.getElementById("delete_modal").addEventListener("hidden.bs.modal", (event) => {
              delete window.deletePostData && $("#delete-records-number").html("");
            });
        },
      }),
    config.buttons.hasOwnProperty("manipulateSelected") &&
      buttons.push({
        className: config.buttons.manipulateSelected.className + " ",
        text: config.buttons.manipulateSelected.text,
        action: function () {
          postData = { selections: mapSelection(this.rows({ selected: !0 })[0], this.ajax.json().ids), request_type: "manipulate-selected" };
          Object.assign(postData, config.buttons.manipulateSelected.postData),
            $.ajax({
              type: "POST",
              url: config.buttons.manipulateSelected.url,
              data: postData,
              success: function (response) {
                config.buttons.manipulateShown.redirect ? (window.location.href = JSON.parse(response).url) : (response = JSON.parse(response)).reload && location.reload();
              },
            });
        },
      }),
    config.buttons.hasOwnProperty("manipulateShown") &&
      buttons.push({
        className: config.buttons.manipulateShown.className + " ",
        text: config.buttons.manipulateShown.text,
        action: function () {
          var postData = { shown_data: this.ajax.json().ids, request_type: "manipulate-shown" };
          Object.assign(postData, config.buttons.manipulateShown.postData),
            $.ajax({
              type: "POST",
              url: config.buttons.manipulateShown.url,
              data: postData,
              success: function (response) {
                config.buttons.manipulateShown.redirect ? (window.location.href = JSON.parse(response).url) : (response = JSON.parse(response)).reload && location.reload();
              },
            });
        },
      }),
    config.buttons.hasOwnProperty("custom") &&
      buttons.push({
        text: config.buttons.custom.text,
        action: config.buttons.custom.action,
        className: config.buttons.custom.className + " ",
        attr: { type: "button", title: config.buttons.custom.title },
      }),
    {
      buttons: { dom: { buttonLiner: { tag: "span" } }, buttons: buttons },
      processing: !0,
      serverSide: !0,
      select: !0,
      order: [[0, "asc"]],
      dom: "Bfrtip",
      lengthMenu: [
        [10, 25, 50, 250],
        ["10 baris", "25 baris", "50 baris", "250 baris"],
      ],
      language: { url: baseUrl("js/datatables/indonesian.json") },
      searchDelay: 500,
      scrollX: !0,
      ajax: config.ajax,
      columns: config.columns,
      drawCallback: config.drawCallback,
    }
  );
}

function mapSelection(selected, all) {
  return selected.map(function (e) {
    return all[e];
  });
}

var postData = () => {
  var postData = [];
  postData[$("#secureData").attr("name")] = $("#secureData").val();
  return { ...postData };
};

const limitText = (str) => {
  if (str.length > 24) {
    return str.substring(0, 21) + "...";
  }
  return str;
};
