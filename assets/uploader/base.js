import Sortable from "sortablejs";
import Dropzone from "dropzone"
export function createDropzone(el, options = {}) {
    if (el.dropzone) return;

    const dz = new Dropzone(el, {
        url: el.dataset.uploadUrl,

        parallelUploads: 1, //  zásadní

        paramName: "file",
        addRemoveLinks: true,
        dictRemoveFile: "Odstranit",
        dictDefaultMessage: "Přetáhnout soubory nebo kliknout pro nahrání",
        dictCancelUpload: "Zrušit upload",
        dictCancelUploadConfirmation: "Opravdu chcete zrušit upload?",
        dictUploadCanceled: "Upload zrušen",
        dictFileTooBig: "Soubor je příliš velký ({{filesize}} MB). Max: {{maxFilesize}} MB.",
        dictInvalidFileType: "Nepodporovaný typ souboru",
        ...options
    });


    dz.on("success", (file, response) => {
        file.serverId = response.id;

        dz.emit("thumbnail", file, response.thumbUrl);

        if (file.previewElement) {
            const input = file.previewElement.querySelector(".dz-filename-input");
            if (input) {
                input.value = response.name;
            }
        }

        if (response.size && file.previewElement) {
            const sizeWrapper = file.previewElement.querySelector(".dz-size");

            if (sizeWrapper) {
                const originalSize = file.size;
                const optimizedSize = response.size;

                const percent = Math.round(100 - (optimizedSize / originalSize) * 100);

                const optimizedText = dz.filesize(optimizedSize);

                const newSize = document.createElement("div");
                newSize.className = "dz-optimized-size";

                newSize.innerHTML = `

                    -${percent}% ${optimizedText}

            `;

                // vlož pod původní velikost
                sizeWrapper.insertAdjacentElement("afterend", newSize);
            }
        }
        reorder();
    });


    dz.on("removedfile", (file) => {
        if (file.serverId) {
            fetch(`${el.dataset.removeUrl}${file.serverId}`, {
                method: "DELETE",
            });
        }
    });

    dz.on("addedfile", (file) => {
        file.previewElement.dropzoneFile = file;

        const nameEl = file.previewElement.querySelector("[data-dz-name]");

        if (nameEl) {
            const currentName = nameEl.innerText;

            const input = document.createElement("input");
            input.type = "text";
            input.maxLength = 255;
            input.value = currentName;
            input.className = "dz-filename-input form-control form-control-sm";
            input.name = "fileName[]"

            nameEl.replaceWith(input);

            // 🔥 save při změně
            input.addEventListener("change", () => {
                if (!file.serverId) return;

                fetch(`${el.dataset.renameUrl}${file.serverId}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        nazev: input.value
                    })
                });
            });
        }

    });


    //  NAČTENÍ EXISTUJÍCÍCH SOUBORŮ
    const entity = el.dataset.entity;
    const entityId = el.dataset.entityId;

    if (entity && entityId) {
        fetch(`${el.dataset.listUrl}${entity}/${entityId}`)
            .then(res => res.json())
            .then(files => {
                files.forEach(file => {
                    const mockFile = {
                        name: file.name,
                        size: file.size
                    };

                    dz.emit("addedfile", mockFile);
                    dz.emit("thumbnail", mockFile, file.thumbUrl);
                    dz.emit("complete", mockFile);

                    mockFile.serverId = file.id;
                });
            });
    }

    new Sortable(el, {
        draggable: ".dz-preview",
        handle: ".dz-details", // 🔥 tahání jen za obrázek

        // zakázat drag z interaktivních prvků
        filter: ".dz-remove, input",
        preventOnFilter: false,

        animation: 150,

        onEnd: () => {
            reorder();
        }
    });

    function reorder() {
        // tady získáš nové pořadí
        const order = [];

        el.querySelectorAll(".dz-preview").forEach((preview, index) => {
            const file = preview.dropzoneFile;

            if (file && file.serverId) {
                order.push({
                    id: file.serverId,
                    position: index
                });
            }
        });


        // pošli na backend
        fetch(`${el.dataset.reorderUrl}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(order)
        });
    }
}


