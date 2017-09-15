<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Measures template
//
//-->
<xsl:template match="measures">
<form action="/{/output/page}/{@case_id}" method="post">
<table class="table table-condensed table-striped table-hover iso table-xs">
<thead>
<tr>
<th class="isonr">#</th>
<th>Maatregel uit <xsl:value-of select="@iso" /></th>
<th class="risk">Urgentie</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="measure">
<xsl:if test="@category">
<tr class="category">
<td colspan="3"><xsl:value-of select="@category" /></td>
</tr>
</xsl:if>
<tr class="measure" onClick="javascript:$('.extra_{@id}').slideToggle(0)">
<td class="relevant_{@relevant}"><xsl:value-of select="number" /></td>
<td class="name relevant_{@relevant}"><xsl:value-of select="name" /></td>
<td class="{risk} relevant_{@relevant}"><xsl:value-of select="risk" /></td>
</tr>
<xsl:if test="count(threat)>0">
	<tr class="extra extra_{@id}"><td></td><td colspan="2"><table class="extra">
	<xsl:for-each select="threat">
		<tr>
		<td class="relevant_{@relevant}"><xsl:value-of select="@number" />: <xsl:value-of select="." /></td>
		<td class="relevant_{@relevant}"><xsl:value-of select="@risk" />, <xsl:value-of select="@handle" /></td>
		</tr>
	</xsl:for-each>
	<tr><td colspan="2"><input type="checkbox" name="iso_measures[]" value="{@id}" onClick="javascript:enable_save_button()"><xsl:if test="@overruled='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input> Deze maatregel <xsl:value-of select="@select" />.</td></tr>
	</table></td></tr>
</xsl:if>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/rapport/{@case_id}" class="btn btn-default">Verder naar rapportage</a>
<input type="submit" id="submit_button" value="Meenemen en negeren van maatregelen opslaan" class="save btn btn-default" />
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="breadcrumbs" />
<h1>ISO maatregelen</h1>
<div class="case"><xsl:value-of select="case" /></div>
<xsl:apply-templates select="measures" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
