<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Report template
//
//-->
<xsl:template match="report">
<form action="{/output/page/@url}" method="post">
<input type="submit" value="Rapportage maken" class="btn btn-default" />
<div><input type="checkbox" name="not_relevant" /> Punten zonder opmerking toevoegen</div>
</form>

<div class="btn-group">
<a href="/pia/casus" class="btn btn-default">Terug naar casussen</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="breadcrumbs" />
<h1>PIA rapportage</h1>
<xsl:apply-templates select="report" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
