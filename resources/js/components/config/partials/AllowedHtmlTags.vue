<template>
    <div>
        <button class="btn btn-link px-0 py-0" type="button" data-toggle="collapse" data-target="#allowedHtml"
                aria-expanded="false" aria-controls="allowedHtml">
            Hinweise zum HTML & Styling
        </button>
        <div class="collapse mb-3" id="allowedHtml">
            <table class="table table-sm mb-0">
                <tbody>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>Styling</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>Die HTML-Vorlage nutzt das <a href="https://getbootstrap.com/docs/4.6/components/alerts/" target="_blank">Bootstrap 4.6 Styling</a>. Entsprechende CSS-Klassen können genutzt werden.</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>Erlaubte HTML Attribute</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>style, class, href, src, alt, width, height, colspan</span>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-4 font-weight-normal border-0">
                        <span>Erlaubte CSS Eigenschaften</span>
                    </td>
                    <td class="col-8 font-weight-normal border-0">
                        <span>font, font-size, font-weight, font-style, font-family, text-decoration, padding-left, padding-right, padding-bottom, padding-left, margin-top, margin-right, margin-bottom, margin-left, color, background-color, text-align, border, border-top, border-right, border-bottom, border-left, max-width, min-width, width, height</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {};
</script>
