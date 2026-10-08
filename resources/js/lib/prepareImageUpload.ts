const PREVIEW_EDGE = 2048;
const COMPRESS_THRESHOLD = 2 * 1024 * 1024;

/**
 * Reduce large camera photos before previewing or sending them to the server.
 * createImageBitmap's resize option lets mobile browsers decode a smaller
 * bitmap, which keeps high-resolution camera images from exhausting memory.
 */
export async function prepareImageForUpload(file: File): Promise<File> {
    if (
        file.size < COMPRESS_THRESHOLD ||
        !file.type.startsWith("image/") ||
        file.type === "image/gif" ||
        typeof createImageBitmap !== "function"
    ) {
        return file;
    }

    let bitmap: ImageBitmap;

    try {
        bitmap = await createImageBitmap(file, {
            resizeWidth: PREVIEW_EDGE,
            resizeQuality: "high",
        });
    } catch {
        // Keep the original so the upload endpoint can return a clear format error.
        return file;
    }

    try {
        const scale = Math.min(
            1,
            PREVIEW_EDGE / bitmap.width,
            PREVIEW_EDGE / bitmap.height,
        );
        const width = Math.max(1, Math.round(bitmap.width * scale));
        const height = Math.max(1, Math.round(bitmap.height * scale));
        const canvas = document.createElement("canvas");
        canvas.width = width;
        canvas.height = height;

        const context = canvas.getContext("2d");
        if (!context) return file;

        context.drawImage(bitmap, 0, 0, width, height);

        const outputType = ["image/jpeg", "image/png", "image/webp"].includes(
            file.type,
        )
            ? file.type
            : "image/jpeg";
        const blob = await new Promise<Blob | null>((resolve) =>
            canvas.toBlob(resolve, outputType, 0.86),
        );

        if (!blob || blob.size >= file.size) return file;

        const extension =
            outputType === "image/png"
                ? "png"
                : outputType === "image/webp"
                  ? "webp"
                  : "jpg";
        const baseName = file.name.replace(/\.[^.]+$/, "") || "foto";

        return new File([blob], `${baseName}.${extension}`, {
            type: outputType,
            lastModified: file.lastModified,
        });
    } finally {
        bitmap.close();
    }
}

export function revokePreviewUrl(url: string): void {
    if (url.startsWith("blob:")) URL.revokeObjectURL(url);
}
