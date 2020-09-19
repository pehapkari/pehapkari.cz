const HtmlWebpackPlugin = require('html-webpack-plugin')
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const devMode = process.env.NODE_ENV !== "production";

module.exports = {
    entry: './src/styles.css',
    mode: process.env.NODE_ENV,
    module: {
        rules: [
            {
                test: /\.(css)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: false,
                            importLoaders: 2,
                            modules: false
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            ident: 'postcss',
                            plugins: [
                                require('tailwindcss'),
                                require('autoprefixer'),
                                require('cssnano')({
                                    preset: 'default',
                                }),
                            ],
                        },
                    },
                ],
            },


        ],
    },
    plugins: [
        new HtmlWebpackPlugin({
            filename: 'index.html',
            template: 'src/index.html',
        }),
        // extract css
        new MiniCssExtractPlugin({
            filename: !devMode ? "[name].[chunkhash:8].bundle.css" : "[name].bundle.css",
        }),
    ],
}