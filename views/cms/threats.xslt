<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-condensed table-striped table-hover">
<thead>
<tr>
<th>#</th>
<th>Threat</th>
<th class="links">Links</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="threats/threat">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="number" /> / <xsl:value-of select="category_id" /></td>
<td><xsl:value-of select="threat" /></td>
<td><xsl:value-of select="links" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="right">
<xsl:apply-templates select="pagination" />
</div>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New threat</a>
<a href="/cms" class="btn btn-default">Back</a>
<a href="/{/output/page}/categories" class="btn btn-default">Threat categories</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="threat/@id">
<input type="hidden" name="id" value="{threat/@id}" />
</xsl:if>

<label for="number">Number:</label>
<input type="text" id="number" name="number" value="{threat/number}" class="form-control" />
<label for="threat">Threat:</label>
<input type="text" id="threat" name="threat" value="{threat/threat}" class="form-control" />
<label for="description">Description:</label>
<textarea name="description" class="form-control"><xsl:value-of select="threat/description" /></textarea>
<label for="category_id">Category:</label>
<select id="category_id" name="category_id" class="form-control"><xsl:variable name="cat_id" select="threat/category_id" /><xsl:for-each select="categories/category">
<option value="{@id}"><xsl:if test="@id=$cat_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each></select>
<label for="confidentiality">Confidentiality:</label>
<select id="confidentiality" name="confidentiality" class="form-control"><xsl:variable name="value" select="threat/confidentiality" /><xsl:for-each select="cia/option">
<option value="{.}"><xsl:if test=".=$value"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each></select>
<label for="integrity">Integrity:</label>
<select id="integrity" name="integrity" class="form-control"><xsl:variable name="value" select="threat/integrity" /><xsl:for-each select="cia/option">
<option value="{.}"><xsl:if test=".=$value"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each></select>
<label for="availability">Availability:</label>
<select id="availability" name="availability" class="form-control"><xsl:variable name="value" select="threat/availability" /><xsl:for-each select="cia/option">
<option value="{.}"><xsl:if test=".=$value"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each></select>

<h2>ISO measures</h2>
<div class="iso_measures">
<xsl:for-each select="iso_measures/measure">
<div class="measure">
<div class="title"><input type="checkbox" name="iso_links[]" value="{@id}"><xsl:if test="@checked='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input> <span onClick="javascript:$('#desc_{@id}').slideToggle(0)"><xsl:value-of select="title" /></span></div>
<div class="description" id="desc_{@id}"><xsl:value-of select="description" /></div>
</div>
</xsl:for-each>
</div>

<div style="clear:both"></div>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save threat" class="btn btn-default" />
<input type="button" value="Cancel" class="btn btn-default" onClick="javascript:document.location='/{/output/page}'" />
<xsl:if test="threat/@id">
<input type="submit" name="submit_button" value="Delete threat" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<div class="standard"><xsl:value-of select="standard" /></div>
<h1><img src="/images/icons/threats.png" class="title_icon" />Threat administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
