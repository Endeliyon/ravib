<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Report template
//
//-->
<xsl:template match="report">
<div class="border">
<form action="{/output/page/@url}" method="post">
<input type="submit" name="submit_button" value="Rapportage maken" class="btn btn-default" />
<div><input type="checkbox" name="not_relevant" /> Niet relevante maatregelen toevoegen</div>
<div><input type="checkbox" checked="checked" name="sort_by_risk" /> Sorteer op hoogte van risico en urgentie</div>
</form>

<form action="{/output/page/@url}" method="post">
<input type="submit" name="submit_button" value="SOA werkblad" class="btn btn-default" />
<div><input type="checkbox" checked="checked" name="semicolon" /> Gebruik puntkomma</div>
</form>
</div>

<div class="btn-group">
<a href="/casus" class="btn btn-default">Terug naar casussen</a>
<a href="/voortgang/{../case/@id}" class="btn btn-default">Verder naar voortgang</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="breadcrumbs" />
<h1>Rapportage</h1>
<div class="case"><xsl:value-of select="case" /></div>
<xsl:apply-templates select="report" />
<xsl:apply-templates select="result" />

<div id="help">
<p>Het SOA werkblad is een CSV (Comma Separated Value) bestand dat onder andere met Microsoft Excel kan worden ingelezen. De velden in zo'n bestanden worden, zoals de naam al doet vermoeden, gescheiden door middel van een komma. Echter, de Nederlandse versie van Excel gebruikt een punt-komma als scheidingsteken. Selecteer dus deze optie indien u de Nederlandse versie van Excel gebruikt.</p>
</div>
</xsl:template>

</xsl:stylesheet>
