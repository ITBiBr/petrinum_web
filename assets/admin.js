import { createDropzone } from "./uploader/base.js";
import Dropzone from "dropzone";


import "dropzone/dist/dropzone.css";
import './styles/admin.scss';
// vypne auto-init (DŮLEŽITÉ)
Dropzone.autoDiscover = false;

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".dropzone").forEach((el) => {
        const type = el.dataset.type;
        console.log(type);

        if (type === "file") {
            createDropzone(el, {});
        }
        if (type === "image") {
            createDropzone(el, {acceptedFiles: "image/*"});
        }
    });
});

