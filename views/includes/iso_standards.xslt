<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="iso_standards">
<div class="iso_standard">
<form action="/{/output/page}" method="post">
<input type="hidden" name="submit_button" value="standard" />
ISO standard: <select name="iso_standard" onChange="javascript:submit()" class="text">
<xsl:for-each select="standard">
<option value="{@id}"><xsl:if test="@selected='yes'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
</form>
</div>
</xsl:template>

</xsl:stylesheet>
