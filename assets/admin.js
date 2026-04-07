import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";

// vypne auto-init (DŮLEŽITÉ)
Dropzone.autoDiscover = false;

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".dropzone").forEach((el) => {
        if (el.dropzone) return;

        const dz = new Dropzone(el, {
            url: el.dataset.uploadUrl,
            paramName: "file",
            addRemoveLinks: true,
            dictRemoveFile: "Odstranit",
            dictDefaultMessage: "Přetáhnout soubory nebo kliknout pro nahrání",
            createImageThumbnails: true,
            thumbnailWidth: 200,
            thumbnailHeight: 200,
            thumbnailMethod: "contain",
        });


        dz.on("success", (file, response) => {
            file.serverId = response.id;

            // 🔥 použij thumbnail z backendu
            dz.emit("thumbnail", file, response.thumbUrl);
        });


        dz.on("removedfile", (file) => {
            if (file.serverId) {
                fetch(`/admin/delete-foto/${file.serverId}`, {
                    method: "DELETE",
                });
            }
        });

        //  NAČTENÍ EXISTUJÍCÍCH SOUBORŮ
        const galerieId = el.dataset.galerieId;

        if (galerieId) {
            fetch(`/admin/galerie/${galerieId}/fotos`)
                .then(res => res.json())
                .then(files => {
                    files.forEach(file => {
                        const mockFile = {
                            name: file.name,
                            size: file.size
                        };

                        dz.emit("addedfile", mockFile);
                        dz.emit("thumbnail", mockFile, file.thumbUrl); // 🔥
                        dz.emit("complete", mockFile);

                        mockFile.serverId = file.id;
                    });

                });
        }
    });
});

